const path = require('path');
const webpack = require('webpack');

// バンドル設定
module.exports = {
    mode: 'production',
    devtool: false,
    entry: path.resolve(__dirname, "assets/js/index.js"),
    output: {
        path: path.resolve(__dirname, 'public/js'),
        publicPath: '/js/',
        filename: 'bundle.js'
    },
    plugins: [
        new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            moment: "moment"
        })
    ],
    module: {
        rules: [
            {
                test: /\.js$/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            presets: [
                                '@babel/preset-env',
                            ]
                        }
                    }
                ]
            },
            {
                test: /\.(sa|sc|c)ss$/i,
                use: [
                    'style-loader',
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                        }
                    },
                    'sass-loader'
                ],
            },
            {
                test: /\.(ttf|eot|svg|gif|woff|woff2)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            publicPath: '/js/',
                        }
                    }
                ]
            }
        ]
    },
    performance: {
        hints: false
    }
};

//const util = require('util');
//console.log(util.inspect(module.exports, {showHidden: false, depth: null}));