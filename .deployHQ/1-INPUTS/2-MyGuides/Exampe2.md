Skip to main contentSkip to navigation
Header
Primary Navigation
DeployHQ
Welcome
Features
Pricing
What people say
Blog
Help
Get started now
Deploying a Laravel + React Application to FortRabbit Using DeployHQ
Posted on 12th February 2025

Devops & Infrastructure, Frontend, PHP, and Tutorials

Deploying a Laravel + React Application to FortRabbit Using DeployHQ
In today's web development landscape, managing deployments for modern applications can be challenging, especially when dealing with a full-stack application that combines Laravel and React. This guide will walk you through setting up a robust deployment pipeline using DeployHQ to deploy your Laravel and React application to FortRabbit hosting.

Prerequisites
Before we begin, make sure you have:

A Laravel application with React frontend ready for deployment
A Git repository (GitHub, GitLab, or Bitbucket)
A FortRabbit account
A DeployHQ account
1. Preparing Your Laravel + React Application
First, ensure your Laravel and React application is properly structured and ready for production deployment. Your project structure should look something like this:

your-project/
├── app/
├── resources/
│   ├── js/
│   │   ├── components/
│   │   └── app.js
├── public/
├── package.json
├── composer.json
└── .env
Make sure your package.json includes the necessary scripts for building your React application:

{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "watch": "vite build --watch"
  },
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0"
  },
  "devDependencies": {
    "@vitejs/plugin-react": "^4.0.0",
    "laravel-vite-plugin": "^0.7.8",
    "vite": "^4.3.9"
  }
}
2. Setting Up FortRabbit
Creating Your App
Log in to your FortRabbit account
Click "Create new App"
Choose PHP 8.1 or higher
Select your preferred region
Choose the scaling plan that fits your needs
Configure Your App
After creating your app, you'll need to set up the necessary configurations:

Navigate to your app's settings
Set up your MySQL database
Configure your domain settings
Enable SSL if needed
Make note of your deployment credentials, as you'll need them for DeployHQ:

git remote add fortrabbit YOUR_APP_NAME@deploy.REGION.frbit.com:YOUR_APP_NAME.git
3. Configuring DeployHQ
Creating a New Project
Log in to DeployHQ
Click "New Project"
Connect your Git repository
Choose "Laravel" as your project name, for example
Setting Up Your Server
Go to "Servers" in your project
Click "New Server"
Select "SFTP/SSH" as your server type
Enter your FortRabbit SFTP host or IP and credentials, as explained here.
Configure your deployment path:
/srv/app/htdocs
4. Deployment Configuration
In the SSH Commands, we are going to configure:

Before Commands:
php artisan config:cache
php artisan route:cache
php artisan view:cache
After Commands
php artisan migrate --force
Config Files
In DeployHQ, set up a config file called .env with the following values:

APP_ENV=production
APP_KEY=your-app-key
DB_CONNECTION=mysql
DB_HOST=your-fortrabbit-mysql-host
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
5. Automated Build Process
Create a build pipeline in DeployHQ by navigating to Project Settings > Build Pipeline:

Add a New Command, for example called Build Frontend Assets, in which we are going to use node LTS (22.12):
npm ci
npm run build
Another one called Optimize Laravel, with the following commands, using PHP 8.3:
composer install --no-dev --optimize-autoloader
6. Continuous Integration Setup
Automatic Deployments
Go to Project Settings > Automatic Deployments
Enable automatic deployments for your main branch
Configure deployment triggers:
branches:
  main:
    - server: Production
      path: /srv/app/htdocs
  staging:
    - server: Staging
      path: /srv/app/htdocs
Deployment Notifications
Set up Slack or email notifications:

Go to Project Settings > Notifications
Add your preferred notification method
Configure notification events:
Deployment started
Deployment successful
Deployment failed
7. First Deployment
To initiate your first deployment:

Commit and push your changes to your repository
In DeployHQ, click "Deploy Now"
Monitor the deployment process
Check the deployment logs for any issues
Verification Steps
After deployment, verify:

Application loads correctly
Frontend assets are properly built
Database migrations completed successfully
Cache configuration is working
API endpoints are responding
8. Maintenance and Best Practices
Zero-Downtime Deployments
To minimise downtime during deployments, add these commands to your deployment script:

php artisan down --render="maintenance"
# Your deployment commands here
php artisan up
Cache Management
Implement proper cache clearing:

// config/deploy.php
return [
    'steps' => [
        'down' => [
            'artisan:down',
        ],
        'deploy' => [
            'optimize:clear',
            'config:cache',
            'route:cache',
            'view:cache',
            'event:cache',
        ],
        'up' => [
            'artisan:up',
        ],
    ],
];
Backup Strategy
Set up automatic backups in FortRabbit:

Configure database backups
Set up file backups
Test restore procedures
Troubleshooting Common Issues
Build Failures
If your build fails, check:

Node.js and PHP versions match your local environment
All dependencies are properly listed in package.json and composer.json
Build scripts are correctly configured
Environment variables are properly set
Deployment Failures
Common deployment issues and solutions:

Permission errors:
chmod -R 775 storage bootstrap/cache
Composer memory limits:
COMPOSER_MEMORY_LIMIT=-1 composer install
Node.js memory issues:
NODE_OPTIONS=--max_old_space_size=4096 npm run build
Conclusion
You now have a fully automated deployment pipeline for your Laravel and React application using DeployHQ and FortRabbit. This setup provides:

Automated builds and deployments
Zero-downtime deployments
Proper cache management
Database migration handling
Frontend asset optimization
Remember to:

Regularly update your dependencies
Monitor your deployment logs
Keep your environment variables secure
Maintain proper backup procedures
Additional Resources
DeployHQ Documentation
FortRabbit Laravel Guide
Laravel Deployment Best Practices
React Production Build
Ready to streamline your deployment process? Sign up for DeployHQ and start automating your deployments today!

Tell us how you feel about this post?

0

0

0

0

0
Enjoy this? Please help us by sharing it…
Share on Twitter
Post to Facebook
Share on LinkedIn
A little bit about the author
Facundo | CTO | DeployHQ | Continuous Delivery & Software Engineering Leadership - As CTO at DeployHQ, Facundo leads the software engineering team, driving innovation in continuous delivery. Outside of work, he enjoys cycling and nature, accompanied by Bono 🐶.

More posts
About DeployHQ
DeployHQ
DeployHQ is a code deployment service that makes it super easy to automate deploying projects from Git, SVN and Mercurial repositories.
Start your 10 day free trial
Footer
Secondary Navigation
DeployHQ
Welcome
Features
Pricing
What people say
Blog
Help
API Docs
Features
Zero Downtime Deployments
Build Pipelines
Deployment Targets
Automatic Deployments
Deployment Templates
Deploy Behind Firewalls
Powerful Integrations
DeployHQ AI
Solutions
Deploy from Github
Deploy from Bitbucket
Deploy from Gitlab
Deploy with Slack Shopify
Deploy Wordpress Apps
Deploy Shopify Apps
Other bits & bobs
Guides & tutorials
Terms of Service
Privacy Policy
Service Status
Changelog & Updates
Follow us on X
DeployHQ
© 2025 saas.group Inc. All rights reserved.

Tree
Proudly powered by Katapult. Running on 100% renewable energy.
