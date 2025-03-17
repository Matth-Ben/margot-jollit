module.exports = (argv) => {
  return {
    plugins: [
      require('autoprefixer'),
      require('@tailwindcss/postcss'),
      ...[argv.mode === 'production' ? require('cssnano') : null],
  ]}
};
