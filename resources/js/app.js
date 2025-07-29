import './bootstrap';

// Isso carrega todos os arquivos CSS do diretório e subdiretórios
const cssModules = import.meta.glob([
  '../css/**/*.css',
  '../css/*.css'
]);

// Importa todos os arquivos encontrados
Object.values(cssModules).forEach(importFn => importFn());

const jsModules = import.meta.glob([
  './**/*.js',
  './**/**/*.js',
]);

// Importa todos os arquivos encontrados
Object.values(jsModules).forEach(importFn => importFn());
