<?php

use yii\db\Migration;

class m251201_000002_search extends Migration
{
    public function safeUp()
    {
        $this->execute('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        $this->execute('CREATE EXTENSION IF NOT EXISTS unaccent');

        $this->execute("
            ALTER TABLE {{%teacher}} ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
                setweight(to_tsvector('dutch', COALESCE(full_name, '')), 'A') ||
                setweight(to_tsvector('dutch', COALESCE(description, '')), 'C')
            ) STORED
        ");

        $this->execute('CREATE INDEX teacher_search_vector_idx ON {{%teacher}} USING GIN (search_vector)');

        $this->execute("
            ALTER TABLE {{%course_node}} ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
                setweight(to_tsvector('dutch', COALESCE(name, '')), 'A') ||
                setweight(to_tsvector('dutch', COALESCE(summary, '')), 'B') ||
                setweight(to_tsvector('dutch', COALESCE(description, '')), 'C')
            ) STORED
        ");

        $this->execute('CREATE INDEX course_node_search_vector_idx ON {{%course_node}} USING GIN (search_vector)');

        $this->execute("
            ALTER TABLE {{%static_content}} ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
                setweight(to_tsvector('dutch', COALESCE(content, '')), 'B')
            ) STORED
        ");

        $this->execute('CREATE INDEX static_content_search_vector_idx ON {{%static_content}} USING GIN (search_vector)');
    }

    public function safeDown()
    {
        $this->execute('DROP INDEX IF EXISTS static_content_search_vector_idx');
        $this->execute('ALTER TABLE {{%static_content}} DROP COLUMN IF EXISTS search_vector');

        $this->execute('DROP INDEX IF EXISTS course_node_search_vector_idx');
        $this->execute('ALTER TABLE {{%course_node}} DROP COLUMN IF EXISTS search_vector');

        $this->execute('DROP INDEX IF EXISTS teacher_search_vector_idx');
        $this->execute('ALTER TABLE {{%teacher}} DROP COLUMN IF EXISTS search_vector');

        $this->execute('DROP EXTENSION IF EXISTS pg_trgm');
        $this->execute('DROP EXTENSION IF EXISTS unaccent');
    }
}