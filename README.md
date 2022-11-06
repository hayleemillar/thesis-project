## Hello and Welcome!
You have found my undergraduate thesis, Roots. Please read my thesis paper, thesis.pdf for an extended explanation of this project in detail. Otherwise, below is the abstract to the paper.

#### Abstract
Poorly-targeted advertisements have plagued the Internet for some time now, which has given way for the subscription model to rise in popularity. Advertisements (ads) have the potential to irritate and deter users, as they are vying for the user’s attention alongside a website’s content. Using a subscription model, users are no longer distracted from the creator’s content with ads, thus increasing user happiness.
Roots is a subscription platform where content creators can monetize their media websites. Users can explore and subscribe to new websites, as well as manage their subscriptions. Creators can create landing pages describing their content site as well as subscription tiers with associated benefits.
Such design & behavior is achieved through using free and open-source software Linux, Nginx, MySQL, and PHP. Using the PHP framework, Symfony, and JSON Web Tokens, Roots sends authentication data to content creator sites alongside an inbound consumer when they desire to leave Roots.

#### Software Requirements:
Lando: https://lando.dev/  
Node Package Manager: https://www.npmjs.com/get-npm

#### How to Run:
1. Clone `git clone git@github.com:hayleemillar/thesis-project.git`
2. Start the project `lando start`
3. Use NPM to install required packages: `npm install`

Success! The website should now be served at https://thesis.lndo.site.  
You should now be able to create a user within a database and view the JWT demo hidden behind the authentication wall. Be sure to hash your password properly!
