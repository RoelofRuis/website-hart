1) Data and indexing design
- For each entity you want searchable (start with `Course`, later `Teacher`, `LessonFormat`):
  - Build a weighted `tsvector` column, e.g. `search_vector` with weights:
    - A: `name` (and `slug`)
    - B: tags, course type
    - C: `summary`
    - D: `description`
  - Use Dutch analyzer if most content is NL: `to_tsvector('dutch', ...)`.
  - Keep it in sync with a trigger, or as a generated column in recent Postgres.
  - Add a GIN index on the vector: `CREATE INDEX ... USING GIN (search_vector);`
  - Also add `pg_trgm` GIN indexes for `ILIKE`/similarity on `name`, and maybe `summary` for typo‑tolerant fallback.

2) Querying and ranking
- Accept `q` and build a tsquery: `websearch_to_tsquery('dutch', :q)` so users can do quotes, minus, OR, etc.
- Primary match with FTS:
  - `WHERE search_vector @@ websearch_to_tsquery('dutch', :q)`
  - Rank: `ts_rank_cd(search_vector, websearch_to_tsquery(...), [weights])`
- Fallback for short or very fuzzy queries:
  - OR condition using trigram similarity on `name`: `name % :q` or `similarity(name, :q) > 0.3`
- Blend ranking:
  - Prefer exact/prefix name matches, then vector rank, then alphabetical.
  - Optionally boost by recency/popularity: `rank * 0.8 + log1p(views) * 0.2`.
- Highlighting for snippets:
  - Use `ts_headline('dutch', description, websearch_to_tsquery(...))` in the SELECT for showing context in results.

3) Cross‑entity search (unified results)
- Introduce a small `SearchService` that queries each entity, normalizes into a common DTO `{type, id, title, url, snippet, score, badges}`.
- Start with UNION ALL SQL or two queries; paginate and sort by unified score.
- Later, move to a materialized view or a dedicated `search_index` table updated via triggers for speed.

4) Yii integration (server)
- Minimal change to `CourseController::actionIndex()` or add a dedicated `SearchController::actionIndex()` that:
  - Reads `q` from GET.
  - Calls `SearchService::search(q, facets, page)`.
  - Returns `ActiveDataProvider` or plain array for rendering.
- Add an AJAX endpoint `/search/suggest?q=...` that returns top 5 suggestions from `name` with trigram similarity + prefix.

5) Yii integration (UI)
- Global search box in the main navbar (right side):
  - Single input with submit → `/search?q=...`.
  - Keyboard shortcut `/` to focus, `Esc` to clear.
- Results page
  - Tabs or pill filters: All | Courses | Teachers | Lesson formats.
  - Facets: tags, course type, location, teacher; show counts (can come later).
  - Sort by: Relevance (default), Name A–Z.
  - Result cards: title, badges (Course Type/Tags), highlighted snippet, matched teacher names, and a “View” CTA.
  - Empty state with helpful suggestions and link to browse.
- Autocomplete/typeahead (progressive enhancement):
  - On typing 2+ chars, call `/search/suggest` and render a dropdown with top 5 course names; on Enter go to full results.

6) Performance and quality
- Indexes: GIN on `search_vector`; GIN (trigram) on `name`; consider partial indexes for non‑null.
- Timeouts and caching: set a reasonable statement timeout and cache identical queries for a short TTL.
- Observability: log `q`, response time, hits, and “zero result” queries for tuning.
- Synonyms: maintain a small synonyms list for course aliases; implement via extra tokens appended to the vector or a synonyms dictionary.

7) Internationalization
- Use `websearch_to_tsquery('dutch', ...)` for NL; add language detection or a toggle if you’ll mix EN.
- Disable stemming for names/slugs; index them separately at higher weight.

8) Security & privacy
- Respect visibility/roles in queries (e.g., unpublished courses hidden to guests).
- Rate‑limit suggest endpoint to avoid abuse.

---

### Concrete starting point (MVP checklist)
- DB:
  - Enable extensions: `CREATE EXTENSION IF NOT EXISTS pg_trgm;` and `CREATE EXTENSION IF NOT EXISTS unaccent;` (optional).
  - Course table migration:
    - Add `search_vector tsvector` (generated or maintained by trigger) combining fields with weights.
    - GIN index on `search_vector`.
    - Trigram GIN index on `name`.
- Backend:
  - Add `Course::scopeSearch($q)` that builds the FTS + trigram OR and returns a query with `SELECT ts_rank_cd(...) AS score, ts_headline(...) AS snippet`.
  - New `SearchController::actionIndex()` for cross‑entity orchestration, or extend existing `CourseController::actionIndex()` short‑term.
  - Add `/search/suggest` endpoint for autocomplete from `Course` name.
- UI:
  - Add a search input in the navbar (in `views/layouts/main.php`) posting to `/search` with `q`.
  - Create `views/search/index.php` that renders tabs, facets (stub), and result list with highlighted snippets.

---

### Yii2 snippet examples
- FTS SELECT sketch for courses:
```php
$q = Yii::$app->request->get('q');
$lang = 'dutch';
$tsQuery = new \yii\db\Expression("websearch_to_tsquery(:lang, :q)", [':lang' => $lang, ':q' => $q]);

$query = Course::find()
  ->select([
    'courses.*',
    new \yii\db\Expression('ts_rank_cd(search_vector, ' . $tsQuery->expression . ', 1) AS score', $tsQuery->params),
    new \yii\db\Expression("ts_headline(:lang, COALESCE(summary, description, ''), "
      . $tsQuery->expression .
      ", 'ShortWord=3, MinWords=5, MaxWords=20, HighlightAll=false') AS snippet", [':lang' => $lang] + $tsQuery->params),
  ])
  ->where(['@@', new \yii\db\Expression('search_vector'), $tsQuery])
  ->orWhere(['and', new \yii\db\Expression('similarity(name, :q) > 0.3', [':q' => $q])])
  ->orderBy(['score' => SORT_DESC, 'name' => SORT_ASC]);
```

- Migration sketch for Postgres FTS (generated column version):
```sql
-- Enable extensions
CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE EXTENSION IF NOT EXISTS unaccent;

-- Generated tsvector (PG 12+)
ALTER TABLE courses
  ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
    setweight(to_tsvector('dutch', coalesce(unaccent(name), '')), 'A') ||
    setweight(to_tsvector('dutch', coalesce(unaccent(slug), '')), 'A') ||
    setweight(to_tsvector('dutch', coalesce(unaccent(summary), '')), 'C') ||
    setweight(to_tsvector('dutch', coalesce(unaccent(description), '')), 'D')
  ) STORED;

CREATE INDEX courses_search_vector_gin ON courses USING GIN (search_vector);
CREATE INDEX courses_name_trgm_gin ON courses USING GIN (name gin_trgm_ops);
```

---

### When to consider external search now
- You want strong typo tolerance out‑of‑the‑box, fast facet counts, synonym management UI, or plan for >100k docs with heavy concurrent search.
- Meilisearch/Typesense are simpler to operate than Elasticsearch and integrate well via HTTP; can be added later with a sync job from the DB.

---

### Next steps
1) Confirm searchable entities and fields (Courses now; Teachers/Formats next) and language(s).
2) Implement Option B (FTS + trigram) as above for Courses.
3) Add navbar search + results page with tabs and basic highlighting.
4) Measure queries, add autocomplete; then expand to Teachers and LessonFormats and facets.

If you prefer, I can draft the exact Yii2 migration and controller/service code for the FTS MVP tailored to your schema.