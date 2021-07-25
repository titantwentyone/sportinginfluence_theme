const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const CopyPlugin = require("copy-webpack-plugin");

module.exports = {
    mode: 'development',
    devtool: 'source-map',
    entry: {
        //main: './index.js',
        homepage: { import: './src/styles/homepage.scss'},
        generic: { import: './src/styles/generic.scss'},
        calendar: { import: './src/styles/calendar.scss'},
        booking_css: { import: './src/styles/booking.scss'},
        offcanvas: { import: './src/js/offcanvas.js'},
        booking: { import: './src/js/booking.js'}
    },
    target: 'web',
    
    output: {
        filename: 'js/[name].js',
        clean: true,
        path: path.join(__dirname, "/dist"),
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader
                    },
                    'css-loader',
                    'sass-loader'
                ]
            },
            /*
            {
                test: /\.(php|css)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]'
                }
            },
            */
            {
                test: /\.mp4$/,
                loader: 'file-loader',
                options: {
                    name: 'media/[name].[ext]'
                }
            },
            /*
            {
                test: /screenshot.png/,
                loader: 'file-loader',
                options: {
                    name: 'screenshot.png'
                }
            }
            */
        ]
    },
    devServer: {
        port: 9000,
        index: './homepage.htm',
        host: 'sportinginfluence.local',
        contentBase: path.join(__dirname, 'dist'),
        hot: true,
        watchContentBase: true
    },
    plugins: [
        new CopyPlugin({
            patterns: [
                //'src/vendor/',
                {
                    from: '**/*.php',
                    context: './src/',

                },
                'src/style.css',
                'src/screenshot.png',
                {
                    from: 'media/Site-Vid.mp4',
                    to: 'media/Site-Vid.mp4',
                    context: './src/'
                }
            ]
        }),
        new RemoveEmptyScriptsPlugin(),
        new MiniCssExtractPlugin({
            filename: "styles/[name].css",
          }),
    ]
}
