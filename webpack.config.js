const path = require('path');
const fs = require('fs');

const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const ManifestPlugin = require('webpack-manifest-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');

const CleanWebpackPlugin = require('clean-webpack-plugin')
const CleanWebpackPaths = ['public/assets/frontend'];

const webpack = require('webpack');

const sassVariables = require('./assets/variables.js');

module.exports = (env, argv) => {
    const prodMode = argv.mode === "production";

    return {
        optimization: prodMode ? {
            sideEffects: true,
            minimize: true,
            splitChunks: {
                cacheGroups: {
                    vendors: {
                        test(mod) {
                            // TO DO improvement: https://stackoverflow.com/a/52961891
                            // Only node_modules are needed
                            if (mod.resource && !mod.resource.includes('node_modules')) {
                                return false;
                            }
                            // But not node modules that contain these key words in the path
                            if (['preact', 'react', 'tui-', 'highlight', 'codemirror', 'mark', 'squire', 'entities', 'mdurl', 'linkify', 'uc.micro', 'puny'].some(str => mod.resource && mod.resource.includes(str))) {
                                return false;
                            }
                            return true;
                        },
                        name: 'vendor',
                        chunks: 'all',
                        reuseExistingChunk: true,
                    },
                    styles: {
                        name: 'styles',
                        test: /\.css$/,
                        chunks: 'all',
                        minChunks: 1,
                        reuseExistingChunk: true,
                        enforce: true,
                    },
                }
            },
            runtimeChunk: 'single',
            minimizer: [
                new TerserPlugin({
                    terserOptions: {
                        ecma: undefined,
                        warnings: false,
                        parse: {},
                        compress: {
                            drop_console: true
                        },
                        mangle: true,
                        module: false,
                        output: null,
                        toplevel: false,
                        nameCache: null,
                        ie8: false,
                        keep_classnames: undefined,
                        keep_fnames: false,
                        safari10: false,
                    },
                }),
                new OptimizeCSSAssetsPlugin({
                    cssProcessor: require('cssnano'),
                    cssProcessorOptions: {
                        safe: true,
                        discardComments: {
                            removeAll: true
                        }
                    },
                    canPrint: true,
                    cssProcessorPluginOptions: {
                        preset: ['default', {
                            discardComments: {
                                removeAll: true
                            }
                        }],
                    },
                })
            ]
        } : {},
        entry: {
            app: [
                path.join(__dirname, "assets/js", "common.js"),
                path.join(__dirname, "assets/scss", "index.scss"),
            ],
            "blog-post": path.join(__dirname, "assets/js/views", "blog-post.js"),
            "editor": path.join(__dirname, "assets/js/components", "editor.js"),
        },
        output: {
            filename: '[name].[chunkhash:5].bundle.js',
            chunkFilename: '[name].[chunkhash:5].bundle.js',
            publicPath: "/assets/",
            path: path.join(__dirname, "public/assets/frontend"),
        },
        resolve: {
            alias: {
                'jquery': path.resolve(__dirname, 'node_modules/jquery/dist/jquery.slim.js'),
            },
            extensions: ['.js', '.ts', '.svg', '.css'],
            modules: ["node_modules"]
        },
        module: {
            rules: [{
                test: /\.(js|jsx)$/,
                exclude: /(node_modules)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            ['@babel/preset-env', {
                                modules: false,
                                "targets": {
                                    "ie": "11"
                                }
                            }],
                        ],
                        "plugins": [
                            ['@babel/plugin-proposal-class-properties'],
                            ["@babel/plugin-syntax-dynamic-import"]
                        ],
                    }
                }
            },
            {
                test: /\.(sa|sc|c)ss$/,
                use: [{
                    loader: MiniCssExtractPlugin.loader,
                    options: {
                        publicPath: './'
                    }
                },
                {
                    loader: 'css-loader',
                    options: {
                        url: false,
                        sourceMap: false
                    }
                },
                {
                    loader: 'sass-loader',
                    options: {
                        sourceMap: true
                    }
                },
                {
                    loader: "@epegzz/sass-vars-loader", options: {
                        syntax: 'scss',
                        vars: sassVariables,
                    }
                }],
            },
            {
                test: /\.ts$/,
                use: [{
                    loader: 'ts-loader',
                    options: {
                        compilerOptions: {
                            declaration: false,
                            target: 'es5',
                            module: 'commonjs'
                        },
                        transpileOnly: true
                    }
                }]
            },
            {
                test: /\.(woff|woff2|eot|ttf)(\?[a-z0-9=.]+)?$/,
                use: [{
                    loader: 'url-loader',
                    options: {
                        limit: 100,
                        name: 'fonts/[name].[ext]',
                    }
                }]
            },
            {
                test: /\.(png|jpg|gif)$/,
                use: [
                    {
                        loader: 'url-loader',
                    },
                ],
            },
            ]
        },
        plugins: [
            new MiniCssExtractPlugin({
                filename: "[name].[chunkhash:5].css",
            }),
            new BundleAnalyzerPlugin(),
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery',
            }),
            new ManifestPlugin(),
            new CleanWebpackPlugin(CleanWebpackPaths),
            new CopyPlugin([
                { from: path.join(__dirname, 'node_modules/tui-editor/dist', 'tui-editor-2x.png'), to: path.join(__dirname, "public/assets/") },
                { from: path.join(__dirname, 'node_modules/tui-editor/dist', 'tui-editor.png'), to: path.join(__dirname, "public/assets/") },
            ]),
        ],
    }
}