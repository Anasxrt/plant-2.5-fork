const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const glob = require('glob');

module.exports = (env, argv) => {
  const isProduction = argv.mode === 'production';

  // Build SCSS entries from all SCSS files in scss folder
  const scssEntries = {};
  const scssFiles = glob.sync('./css/scss/*.scss');
  scssFiles.forEach((file) => {
    // Skip partials (files starting with _)
    if (!path.basename(file).startsWith('_')) {
      // Use just the filename without path for the entry name
      const name = path.basename(file, '.scss');
      // Add './' prefix for webpack compatibility
      scssEntries[name] = file.startsWith('./') ? file : `./${file}`;
    }
  });

  return {
    entry: scssEntries,
    output: {
      path: path.resolve(__dirname, 'css'),
      filename: '[name].min.js',
      clean: false,
    },
    module: {
      rules: [
        {
          test: /\.scss$/,
          use: [
            isProduction ? MiniCssExtractPlugin.loader : 'style-loader',
            {
              loader: 'css-loader',
              options: {
                // Don't resolve URLs in CSS - keep them as-is for fonts
                url: false,
              },
            },
            'postcss-loader',
            {
              loader: 'sass-loader',
              options: {
                // Use modern Sass API
                api: 'modern-compiler',
                sassOptions: {
                  quietDeps: true,
                },
              },
            },
          ],
        },
      ],
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: '[name].min.css',
      }),
    ],
    optimization: {
      minimizer: [
        new TerserPlugin({
          terserOptions: {
            compress: {
              drop_console: isProduction,
            },
          },
        }),
        new CssMinimizerPlugin(),
      ],
    },
    devtool: isProduction ? 'source-map' : 'eval-source-map',
    watchOptions: {
      ignored: /node_modules/,
    },
  };
};
