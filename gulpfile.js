var gulp = require('gulp'),
    concat = require('gulp-concat'),
    sass = require('gulp-sass'),
    minify = require('gulp-minify'),
    cleanCSS = require('gulp-clean-css'),
    coffee = require('gulp-coffee');

var VENDOR_ROOT = "./node_modules";
var RESOURCES_ROOT = "./app/Resources/public";

gulp.task('compile:js-vendor', function() {
    gulp.src([
            VENDOR_ROOT + '/jquery/dist/jquery.min.js',
            VENDOR_ROOT + '/bootstrap/dist/js/bootstrap.min.js',
            VENDOR_ROOT + '/underscore/underscore-min.js',
            VENDOR_ROOT + '/backbone/backbone-min.js',
            VENDOR_ROOT + '/backbone.radio/build/backbone.radio.min.js',
            VENDOR_ROOT + '/backbone.marionette/lib/backbone.marionette.min.js'
        ])
        .pipe(concat("libraries.js"))
        .pipe(gulp.dest('./web/js'));
});

gulp.task('compile:js', function() {
    gulp.src(RESOURCES_ROOT + '/js/**/*.coffee')
        .pipe(coffee())
        .pipe(concat("app.js"))
        .pipe(minify())
        .pipe(gulp.dest('./web/js'));
});

gulp.task('compile:css', function() {
    gulp.src([
        VENDOR_ROOT + '/bootstrap/dist/css/bootstrap.css',
        RESOURCES_ROOT + '/css/**/*.scss'
    ])
        .pipe(sass())
        .pipe(cleanCSS())
        .pipe(concat("style.css"))
        .pipe(gulp.dest('./web/css'));
});

gulp.task('compile:fonts', function() {
    gulp.src(VENDOR_ROOT + '/bootstrap/fonts/**/*')
        .pipe(gulp.dest('./web/fonts'));
});

gulp.task('default', [
    'compile:js-vendor', 'compile:js', 'compile:fonts', 'compile:css'
]);

gulp.task('watch', ['default'], function() {
    gulp.watch(RESOURCES_ROOT + '/css/**/*', ['compile:css']);

    gulp.watch(RESOURCES_ROOT + '/js/**/*', ['compile:js']);
});
