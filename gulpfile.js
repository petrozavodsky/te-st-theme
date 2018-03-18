const gulp = require('gulp'),
    gulpLoadPlugins = require('gulp-load-plugins'),
    plugins = gulpLoadPlugins(),
    sourcemaps = require('gulp-sourcemaps'),
    streamqueue = require('streamqueue'),
    urladjuster = require('gulp-css-url-adjuster'),
    imageminJpegRecompress = require('imagemin-jpeg-recompress'),
    imageminPngquant = require('imagemin-pngquant'),
    del = require('del');


gulp.task('i18n', function () {
    return gulp.src([
        '*.php',
        'templates/*.php',
    ])
        .pipe(plugins.wpPot( {
            domain: 'TeStTheme',
            package: 'te-st-theme'
        } ))
        .pipe(gulp.dest('languages/te-st-theme.pot'));

});

gulp.task('theme-less', function () {
    return gulp.src('less/**/*.less')
        .pipe(plugins.plumber())
        .pipe(plugins.concat('css/dest/all.less'))
        .pipe(plugins.less())
        .pipe(plugins.rename('compile-less.css'))
        .pipe(gulp.dest('css/dest'))
        .pipe(plugins.notify({message: 'Собрались стили темы'}));
});

gulp.task('theme_optimize', ['theme-less'], function () {
    return streamqueue({objectMode: true},
        gulp.src('css/vendor/wp-reset.css'),
        gulp.src('css/dest/compile-less.css')
    )
        .pipe(sourcemaps.init())
        .pipe(plugins.concat('css/opt/tmp_concat.css'))
        .pipe(plugins.autoprefixer([
            'ios_saf >= 6',
            'ie 10',
            'opera 12',
            'last 5 versions'
        ]))
        .pipe(urladjuster({
            replace: ['../', '']
        }))
        .pipe(plugins.csso())
        .pipe(plugins.rename('tmp.css'))
        .pipe(sourcemaps.write('/maps'))
        .pipe(gulp.dest('css/opt/'))
        .pipe(plugins.notify({message: 'Оптимизация, прошла'}));
});

gulp.task('concat', ['theme-less', 'theme_optimize'], function () {
    return streamqueue({objectMode: true},
        gulp.src('css/theme_name.css'),
        gulp.src('css/opt/tmp.css')
    )
        .pipe(plugins.concat('css/opt/tmp_concat.css'))
        .pipe(plugins.plumber())
        .pipe(plugins.rename('style.css'))
        .pipe(gulp.dest('./'))
        .pipe(plugins.notify({message: 'Сбилдился css, можно работать'}));
});

gulp.task('build', ['theme-less', 'theme_optimize', 'concat']);

gulp.task('js', function () {
    return gulp.src([
        'js/*.js',
        '!js/*.min.js',
        '!js/vendor/**/*.js'
    ])
        .pipe(plugins.plumber())
        .pipe(plugins.uglify({
            compress: true,
        }))
        .pipe(plugins.rename({
            extname: ".js",
            suffix: ".min"
        }))
        .pipe(gulp.dest(function (file) {
            return file.base;
        }))
        .pipe(plugins.notify({message: 'Скрипты темы собрались'}));
});

gulp.task('svg', function () {
    return gulp.src('images/**/*.svg')
        .pipe(plugins.svgo())
        .pipe(gulp.dest(function (file) {
            return file.base;
        }))
        .pipe(plugins.notify({message: 'SVG оптимизированы'}));
});

gulp.task('img_optimization', function () {
    return gulp.src([
        'images/**/*.png',
        'images/**/*.jpeg',
        'images/**/*.jpg'
    ])
        .pipe(plugins.plumber())
        .pipe(plugins.imagemin([
            plugins.imagemin.gifsicle({interlaced: true}),
            imageminJpegRecompress({
                progressive: true,
                max: 80,
                min: 70
            }),
            imageminPngquant({quality: '80'}),
            plugins.imagemin.svgo({plugins: [{removeViewBox: true}]})
        ]))
        .pipe(gulp.dest(function (file) {
            return file.base;
        }));
});

gulp.task('clean', function (cb) {
    del(['less/maps/*'], cb);
});


gulp.task('watch', function () {
    gulp.watch(['less/**/*.less'], ['build']);
    gulp.watch(
        [
            'js/*.js',
            '!js/*.min.js',
            '!js/vendor/**/*.js'
        ],
        ['js']
    );
});

gulp.task('default', ['watch', 'clean']);

