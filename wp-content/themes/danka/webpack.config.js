const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, argv) => {
  const isProduction = argv.mode === 'production';

  return {
    mode: isProduction ? 'production' : 'development',
    entry: './assets/scripts/app.js',
    output: {
      filename: 'app.js',
      path: path.resolve(__dirname, './assets/build'),
      publicPath: 'http://localhost:8080/wp-content/themes/danka/assets/build/',
      clean: true,
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env'],
            },
          },
        },
        {
          test: /\.css$/,
          use: [
            MiniCssExtractPlugin.loader,
            'css-loader',
            'postcss-loader',
          ],
        },
      ],
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'app.css' }),
    ],
    devServer: {
      allowedHosts: 'all', // Remplace `host` qui peut causer des erreurs
      client: {
        logging: 'info',
        overlay: true
      },
      hot: true,
      liveReload: true,
      port: 8080,
      // watchFiles: ['src/*'], // Active le rechargement automatique
      // static: {
      //   directory: path.resolve(__dirname, 'build'), // Dossier où Webpack Dev Server sert les fichiers
      //   publicPath: '/build/',
      // },
      proxy: [
        {
          context: () => true, // Proxy toutes les requêtes
          target: 'http://localhost:10084',
          changeOrigin: true
        }
      ]
    },
  };
};
