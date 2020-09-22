const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
    resolve: {
        extensions: ['.js', '.vue', '.json', '.less', '.sass'],
        alias: {
            '@': __dirname + '/resources/js'
        },
    }
})
.js('resources/js/app.js', 'public/js')
.sass('resources/sass/app.scss', 'public/css');

mix.js('resources/js/auth/login.js', 'public/js/auth');
mix.js('resources/js/auth/request_link.js', 'public/js/auth');
mix.js('resources/js/auth/reset_password.js', 'public/js/auth');
mix.js('resources/js/auth/register.js', 'public/js/auth');
mix.js('resources/js/auth/register_company.js', 'public/js/auth');

mix.js('resources/js/users/profile.js', 'public/js/users');
mix.js('resources/js/users/onboarding.js', 'public/js/users');

mix.js('resources/js/companies/profile.js', 'public/js/companies');

mix.js('resources/js/jobs/applicants.js', 'public/js/jobs');
mix.js('resources/js/jobs/list.js', 'public/js/jobs');
mix.js('resources/js/jobs/applied-to.js', 'public/js/jobs');
mix.js('resources/js/jobs/post.js', 'public/js/jobs');
mix.js('resources/js/jobs/post-day-labour.js', 'public/js/jobs');
mix.js('resources/js/jobs/view.js', 'public/js/jobs');
mix.js('resources/js/jobs/search.js', 'public/js/jobs');
mix.js('resources/js/jobs/search_all.js', 'public/js/jobs');

mix.js('resources/js/chat/chat.js', 'public/js/chat')

mix.js('resources/js/admin/datatable.js', 'public/js/admin');
mix.js('resources/js/admin/exports.js', 'public/js/admin');
