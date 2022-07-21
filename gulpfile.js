const gulp = require('gulp'),
      eslint = require('gulp-eslint'),
      del = require('del'),
      rename = require('gulp-rename'),
      tap = require('gulp-tap'),
      browserSync = require('browser-sync').create(),
      sass = require('gulp-sass'),
      babelify = require('babelify'),
      browserify = require('browserify'),
      vinylSource = require('vinyl-source-stream'),
      buffer = require('vinyl-buffer'),
      concat = require('gulp-concat'),
      util = require('gulp-util'),
      sourcemaps = require('gulp-sourcemaps'),
      source = 'source',
      dest = 'public',
      libdir = 'node_modules',
      resize = require('gulp-image-resize'),
      pngquant = require('imagemin-pngquant'),
      imagemin= require('gulp-imagemin'),
      ngConfig = require('gulp-ng-config'),
      newer = require('gulp-newer');

gulp.task('scss', () => {
    return gulp.src(`${source}/scss/base.scss`)
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'compressed'}).on('error',sass.logError))
    .pipe(sourcemaps.write())
    .pipe(rename('styles.css'))
    .pipe(gulp.dest(`${dest}/css`));
});

gulp.task('watch-files', (done) => {
    browserSync.reload();
    done();
});

gulp.task('js', () => {
    const bundler = browserify({ debug: true });

	bundler.transform("babelify", {
        presets: ["@babel/preset-env"],
        sourceMaps: true,
        global: true,
        ignore: [/\/node_modules\/(?!your module folder\/)/]
    });
	bundler.add(`${source}/js/scripts.js`);

	return bundler.bundle()
    .on('error', util.log)
    .pipe(vinylSource(`${source}/js/scripts.js`))
    .pipe(buffer())
    .pipe(concat('app.js'))
    .pipe(gulp.dest(`${dest}/js/`));
});

gulp.task('html', () => {
    return gulp.src(`${source}/**/*.html`)
    .pipe(gulp.dest(dest))
});

gulp.task('favicon', () => {
    return gulp.src(`${source}/favicon.ico`)
    .pipe(gulp.dest(dest))
});

gulp.task('volsys', () => {
    return gulp.src(`${source}/volunteer-system/**/*.*`)
    .pipe(gulp.dest(`${dest}/volunteer-system/`))
});

gulp.task('feed', () => {
    return gulp.src(`${source}/feed/**/*.*`)
    .pipe(gulp.dest(`${dest}/feed/`))
});

gulp.task('assessments', () => {
    return gulp.src(`${source}/assessments/**/*.*`)
    .pipe(gulp.dest(`${dest}/assessments/`))
});

gulp.task('decisiontree', () => {
    return gulp.src(`${source}/decision-tree/**/*.*`)
    .pipe(gulp.dest(`${dest}/decision-tree/`))
});

gulp.task('htaccess', () => {
    return gulp.src(`${source}/**/.htaccess`)
    .pipe(gulp.dest(dest))
});

gulp.task('robots', () => {
    return gulp.src(`${source}/**/robots.txt`)
    .pipe(gulp.dest(dest))
});

gulp.task('media', () => {
    return gulp.src(`${source}/media/**/*`)
        .pipe(newer(`${dest}/media`))
        .pipe(gulp.dest(`${dest}/media/`))
});

gulp.task('thumbs', () => {
    return gulp.src(`${source}/images/**/*`)
        .pipe(newer(`${dest}/thumbs`))
        .pipe(tap((file, t)=>{console.log(file.path)}))
        .pipe(resize({
            height: 150,
            crop: false,
            upscale: false,
            imageMagick: false
        }))
		.pipe(imagemin({
            progressive: true,
            use: [pngquant()]
        }))
		.pipe(gulp.dest(`${dest}/thumbs`));
});

gulp.task('images', () => {
    return gulp.src(`${source}/images/**/*`)
        .pipe(newer(`${dest}/images`))
        .pipe(tap((file, t)=>{console.log(file.path)}))
        .pipe(resize({
            width: 1920,
            crop: false,
            upscale: false,
            imageMagick: false
        }))
		.pipe(imagemin({
            progressive: true,
            use: [pngquant()]
        }))
		.pipe(gulp.dest(`${dest}/images`));
});

gulp.task('eslint', () => {
    return gulp.src(`${source}/js/*.js`)
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failOnError());
});


gulp.task('browser-sync', () => {
    browserSync.init({
        files: `${dest}/css/*.css`,
        host: 'jasonmac.local',
        proxy: 'jasonmac.local:8080'
    });

    gulp.watch(`${source}/**/*.scss`, gulp.series('scss'));
    gulp.watch([`${source}/js/*.js`, `${source}/components/**/*.js`], gulp.series('eslint', 'js', 'watch-files'));
    gulp.watch(`${source}/**/*.html`, gulp.series('html', 'watch-files'));
    gulp.watch(`${source}/**/.htaccess`, gulp.series('htaccess', 'watch-files'));
    gulp.watch(`${source}/volunteer-system/**/*.*`, gulp.series('volsys', 'watch-files'));
    gulp.watch(`${source}/feed/**/*.*`, gulp.series('feed', 'watch-files'));
    gulp.watch(`${source}/assessments/**/*.*`, gulp.series('assessments', 'watch-files'));
    gulp.watch(`${source}/decision-tree/**/*.*`, gulp.series('decisiontree', 'watch-files'));
    gulp.watch(`${source}/images/**/*.*`, gulp.series('images', 'thumbs', 'watch-files'));
    gulp.watch(`${source}/media/**/*.*`, gulp.series('media', 'watch-files'));
    gulp.watch(`${source}/fonts/**/*.*`, gulp.series('fonts', 'watch-files'));
    gulp.watch(`${source}/favicon.ico`, gulp.series('favicon', 'watch-files'));
    gulp.watch(`${source}/robots.txt`, gulp.series('robots', 'watch-files'));
});

gulp.task('clean', () => {
    return del([`${dest}/**`, `!${dest}/sitemap.xml`, `!${dest}/images`, `!${dest}/images/**`, `!${dest}/thumbs`, `!${dest}/thumbs/**`, `!${dest}/media`, `!${dest}/media/**`]);
});

gulp.task('prodLib', (cb) => {
    gulp.src(`${libdir}/bootstrap/dist/css/bootstrap.min.css`)
    .pipe(rename('bootstrap.css'))
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/bootstrap/dist/css/bootstrap.min.css.map`)
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/bootstrap/dist/js/bootstrap.bundle.min.js`)
    .pipe(rename('bootstrap.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/bootstrap/dist/js/bootstrap.bundle.min.js.map`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/jquery/dist/jquery.min.js`)
    .pipe(rename('jquery.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/moment/min/moment.min.js`)
    .pipe(rename('moment.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/moment/min/moment.min.js.map`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/clipboard/dist/clipboard.min.js`)
    .pipe(rename('clipboard.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular/angular.min.js`)
    .pipe(rename('angular.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular/angular.min.js.map`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-route/angular-route.min.js`)
    .pipe(rename('angular-route.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-route/angular-route.min.js.map`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-location-update/angular-location-update.min.js`)
    .pipe(rename('angular-location-update.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-sanitize/angular-sanitize.min.js`)
    .pipe(rename('angular-sanitize.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-sanitize/angular-sanitize.min.js.map`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-moment/angular-moment.min.js`)
    .pipe(rename('angular-moment.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-moment/angular-moment.min.js.map`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-moment-picker/dist/angular-moment-picker.min.js`)
    .pipe(rename('angular-moment-picker.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-moment-picker/dist/angular-moment-picker.min.css`)
    .pipe(rename('angular-moment-picker.css'))
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/angular-animate/angular-animate.min.js`)
    .pipe(rename('angular-animate.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-animate/angular-animate.min.js.map`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/ng-ckeditor/dist/ng-ckeditor.min.js`)
    .pipe(rename('ng-ckeditor.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/ngclipboard/dist/ngclipboard.min.js`)
    .pipe(rename('ngclipboard.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-ui-calendar/src/calendar.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`lib/angular-stripe.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/fullcalendar/dist/fullcalendar.min.js`)
    .pipe(rename('fullcalendar.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/fullcalendar/dist/fullcalendar.min.css`)
    .pipe(rename('fullcalendar.css'))
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/@fortawesome/fontawesome-free/css/all.min.css`)
    .pipe(rename('fontawesome.css'))
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/@fortawesome/fontawesome-free/webfonts/*`)
    .pipe(gulp.dest(`${dest}/lib/webfonts/`));
    gulp.src(`${libdir}/ckeditor4/**/*`)
    .pipe(gulp.dest(`${dest}/lib/js/ckeditor/`));
    gulp.src(`${libdir}/ckeditor-youtube-plugin/youtube/**/*`)
    .pipe(gulp.dest(`${dest}/lib/js/ckeditor/plugins/youtube/`));
    cb();
});

gulp.task('devLib', (cb) => {
    gulp.src(`${libdir}/bootstrap/dist/css/bootstrap.css`)
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/bootstrap/dist/css/bootstrap.css.map`)
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/bootstrap/dist/js/bootstrap.bundle.js`)
    .pipe(rename('bootstrap.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/bootstrap/dist/js/bootstrap.bundle.js.map`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/jquery/dist/jquery.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/moment/min/moment.min.js`)
    .pipe(rename('moment.js'))
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/moment/min/moment.min.js.map`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/clipboard/dist/clipboard.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular/angular.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-route/angular-route.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-location-update/angular-location-update.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-sanitize/angular-sanitize.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-moment/angular-moment.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-moment-picker/dist/angular-moment-picker.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-moment-picker/dist/angular-moment-picker.css`)
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/angular-animate/angular-animate.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/ng-ckeditor/dist/ng-ckeditor.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/ngclipboard/dist/ngclipboard.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/angular-ui-calendar/src/calendar.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`lib/angular-stripe.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/fullcalendar/dist/fullcalendar.js`)
    .pipe(gulp.dest(`${dest}/lib/js/`));
    gulp.src(`${libdir}/fullcalendar/dist/fullcalendar.css`)
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/@fortawesome/fontawesome-free/css/all.css`)
    .pipe(rename('fontawesome.css'))
    .pipe(gulp.dest(`${dest}/lib/css/`));
    gulp.src(`${libdir}/@fortawesome/fontawesome-free/webfonts/*`)
    .pipe(gulp.dest(`${dest}/lib/webfonts/`));
    gulp.src(`${libdir}/ckeditor4/**/*`)
    .pipe(gulp.dest(`${dest}/lib/js/ckeditor/`));
    gulp.src(`${libdir}/ckeditor-youtube-plugin/youtube/**/*`)
    .pipe(gulp.dest(`${dest}/lib/js/ckeditor/plugins/youtube/`));
    cb();
});

gulp.task('prod', () => {
    return gulp.src('./config.json')
    .pipe(ngConfig('kotm2020Ui.config', {
        environment: 'prod'
    }))
    .pipe(gulp.dest(`${dest}/js/`));
});

gulp.task('dev', () => {
    return gulp.src('./config.json')
    .pipe(ngConfig('kotm2020Ui.config', {
        environment: 'dev'
    }))
    .pipe(gulp.dest(`${dest}/js/`));
});

gulp.task('staging', () => {
    return gulp.src('./config.json')
    .pipe(ngConfig('kotm2020Ui.config', {
        environment: 'staging'
    }))
    .pipe(gulp.dest(`${dest}/js/`));
});

gulp.task('fonts', () => {
    return gulp.src(`${source}/fonts/**/*`)
    .pipe(gulp.dest(`${dest}/fonts`))
});

exports.buildStaging = gulp.series('clean', 'volsys', 'feed', 'assessments', 'decisiontree', 'htaccess', 'fonts', 'prodLib', 'html', 'scss', 'js', 'favicon', 'robots', 'staging');
exports.buildProd = gulp.series('clean', 'volsys', 'feed', 'assessments', 'decisiontree', 'htaccess', 'fonts', 'prodLib', 'html', 'scss', 'js', 'favicon', 'robots', 'prod');
exports.buildDev = gulp.series('clean', 'volsys', 'feed', 'assessments', 'decisiontree', 'htaccess', 'fonts', 'devLib', 'html', 'scss', 'js', 'favicon', 'robots', 'dev');
exports.images = gulp.series('images');
exports.thumbs = gulp.series('thumbs');