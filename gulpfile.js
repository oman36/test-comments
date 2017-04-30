"use strict";
var gulp        = require('gulp');
var concat      = require('gulp-concat');
var minifyCss   = require('gulp-minify-css');
var sourcemaps  = require('gulp-sourcemaps');
var uglify      = require('gulp-uglify');
var less        = require('gulp-less');
var bowserSync  = require('browser-sync').create();
var LessAutoprefix = require('less-plugin-autoprefix');
var gulpIf      = require('gulp-if');
var gulpClean   = require('gulp-clean');
var autoprefix  = new LessAutoprefix({ browsers: ['last 5 versions'] });

const IS_PROD = process.env.NODE_ENV !== 'development';

gulp.task('serve',function () {
    bowserSync.init();
    bowserSync.watch([
        'public/css/**/*.css',
    ]).on('change',bowserSync.reload);
});

gulp.task('less', function () {
    return gulp.src([
            'less/style.less'
        ]
    )
        .pipe(gulpIf(!IS_PROD,sourcemaps.init()))
        .pipe(less({
            plugins: [autoprefix]
        }))
        .pipe(concat('style.css'))
        .pipe(minifyCss())
        .pipe(gulpIf(!IS_PROD,sourcemaps.write('./')))
        .pipe(gulp.dest('./public/css/'));
});

gulp.task('js', function () {
    return gulp.src([
        'js/jquery-3.1.1.min.js',
        'js/bootstrap.min.js',
        'js/script.js',
    ])
        .pipe(gulpIf(!IS_PROD,sourcemaps.init()))
        .pipe(concat('script.js'))
        .pipe(uglify())
        .pipe(gulpIf(!IS_PROD,sourcemaps.write('./')))
        .pipe(gulp.dest('./public/js/'));
});


gulp.task('watch',function(){
    gulp.watch([
        'less/**/*.less'
    ],['less']);
    gulp.watch('js/**/*.js',['js']);
});

gulp.task('default',function () {

    var tasks = [
        'js',
        'less'
    ];
    if (!IS_PROD) {
        tasks.push('serve');
        tasks.push('watch');
    } else {
        gulp.src([
            'public/css/*.map',
            'public/js/*.map'
        ],{read:false}).pipe(gulpClean());
    }
    gulp.start(tasks);
});