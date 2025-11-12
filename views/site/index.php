<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = 'HART Music School';
?>
<div class="site-index">

    <section class="hero text-center text-white d-flex align-items-center" style="min-height: 50vh; background: linear-gradient(135deg,#4e54c8,#8f94fb); border-radius: .5rem;">
        <div class="container py-5">
            <h1 class="display-4 fw-bold mb-3">Welcome to HART Music School</h1>
            <p class="lead mb-4">Discover inspiring teachers and courses for every level. Start your musical journey today.</p>
            <div class="d-flex justify-content-center gap-2">
                <?= Html::a('Browse Teachers', ['teacher/index'], ['class' => 'btn btn-light btn-lg px-4']) ?>
                <?= Html::a('Explore Courses', ['course/index'], ['class' => 'btn btn-outline-light btn-lg px-4']) ?>
            </div>
        </div>
    </section>

    <div class="body-content mt-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="p-4 h-100 shadow-sm border rounded-3">
                    <h3 class="mb-2">Expert Teachers</h3>
                    <p>Learn from experienced musicians passionate about sharing their craft.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-4 h-100 shadow-sm border rounded-3">
                    <h3 class="mb-2">All Levels</h3>
                    <p>From beginners to advanced students, find the right course for you.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-4 h-100 shadow-sm border rounded-3">
                    <h3 class="mb-2">Community</h3>
                    <p>Join a vibrant local community with ensembles, workshops, and concerts.</p>
                </div>
            </div>
        </div>
    </div>
</div>
