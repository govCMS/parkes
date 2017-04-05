var gulp               = require('gulp');
var del                = require('del');
var gulpLoadPlugins    = require('gulp-load-plugins');
var bs                 = require('browser-sync');
var kss                = require('kss');
var path               = require('path');

var options = {};

options.rootPath = {
  project     : __dirname + '/',
  styleGuide  : __dirname + '/styleguide/',
  theme       : __dirname + '/'
};

options.theme = {
  root  : options.rootPath.theme,
  css   : options.rootPath.theme + 'css/',
  sass  : options.rootPath.theme + 'sass/',
  js    : options.rootPath.theme + 'js/'
};

var sassFiles = [
  options.theme.sass + '**/*.scss',
  // Do not open Sass partials as they will be included as needed.
  '!' + options.theme.sass + '**/_*.scss',
  // Hide additional files
  '!' + options.theme.sass + 'init/uikit/uikit.scss'
];

// Set the URL used to access the Drupal website under development. This will
// allow Browser Sync to serve the website and update CSS changes on the fly.
options.drupalURL = 'health.local';
// options.drupalURL = 'http://localhost'

// Define the style guide paths and options.
options.styleGuide = {
  source: [
    options.theme.sass,
    options.theme.css + 'style-guide/'
  ],
  destination: options.rootPath.styleGuide,

  builder: 'builder/twig',

  // The css and js paths are URLs, like '/misc/jquery.js'.
  // The following paths are relative to the generated style guide.
  css: [
    path.relative(options.rootPath.styleGuide, options.theme.css + 'styles.css'),
    path.relative(options.rootPath.styleGuide, options.theme.css + 'style-guide/kss-only.css')
  ],
  js: [
  ],

  homepage: 'homepage.md',
  title: 'Parkes Style Guide'
}

var $ = gulpLoadPlugins();

var AUTOPREFIXER_BROWSERS = [
  'ie >= 10',
  'ie_mob >= 10',
  'ff >= 30',
  'chrome >= 34',
  'safari >= 7',
  'opera >= 23',
  'ios >= 7',
  'android >= 4.4',
  'bb >= 10'
];

// Clean all directories.
gulp.task('clean', ['clean:css', 'clean:styleguide']);

// Clean style guide files.
gulp.task('clean:styleguide', function() {
  // You can use multiple globbing patterns as you would with `gulp.src`
  return del([
      options.styleGuide.destination + '*.html',
      options.styleGuide.destination + 'public',
      options.theme.css + '**/*.twig'
    ], {force: true});
});

// Clean CSS files.
gulp.task('clean:css', function() {
  return del([
      options.theme.css + '**/*.css',
      options.theme.css + '**/*.map'
    ], {force: true});
});

// Compile and automatically prefix stylesheets
gulp.task('styles', ['clean:css'], function(){

  return gulp.src(sassFiles)
    .pipe($.sourcemaps.init())
    .pipe($.sass({
      precision: 10,
      outputStyle: 'compressed'
    }).on('error', $.sass.logError))
    .pipe($.autoprefixer(AUTOPREFIXER_BROWSERS))
    .pipe($.sourcemaps.write('./'))
    .pipe($.size({title: 'Styles'}))
    .pipe(gulp.dest(options.theme.css));
});

gulp.task('styleguide', ['clean:styleguide'], function() {
  return kss(options.styleGuide);
})

gulp.task('browser-sync', function() {
  bs.init({
    proxy: options.drupalURL,
    open: false
  });
});

// Watch files for changes & reload
gulp.task('watch', ['browser-sync'], function(){
  gulp.watch([options.theme.sass + '/**/*.scss'], ['styles', 'styleguide', bs.reload]);
  gulp.watch([options.theme.sass + '**/*.twig'], ['styleguide', bs.reload]);
  gulp.watch(['./templates/*.php', './*.php']).on('change', bs.reload);
});

// Watch and reload
gulp.task('default', ['styles', 'styleguide', 'watch']);
