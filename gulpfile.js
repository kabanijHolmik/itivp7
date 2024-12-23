const { src, dest, watch, series } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const concat = require('gulp-concat');

// Пример задачи: компиляция SCSS в CSS
function compileSass() {
  return src('src/scss/**/*.scss') // Путь к вашим SCSS-файлам
    .pipe(sass().on('error', sass.logError))
    .pipe(dest('dist/css'));
}

// Пример задачи: объединение JS-файлов
function bundleJs() {
  return src('src/js/**/*.js') // Путь к вашим JS-файлам
    .pipe(concat('app.js'))
    .pipe(dest('dist/js'));
}

// Наблюдение за изменениями
function watchFiles() {
  watch('src/scss/**/*.scss', compileSass);
  watch('src/js/**/*.js', bundleJs);
}

// Экспорт задач
exports.default = series(compileSass, bundleJs, watchFiles);