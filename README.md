# Mini blog API 

Mini blog API  is a RESTful API built on top of [API Platform](https://api-platform.com/) and [Symfony 5](https://symfony.com/).

Its purpose is to provide essential REST endpoints to start a blog application.


# Table of content

- **[Overview](#Overview)**
- **[Development](#Development)**
	- **[Installation](#installation)**
	- **[Testing](#testing)**

# Overview 

- Anyone can register to the application and see all blog posts. 
- Only verified users can add and edit their blog posts. 
- In order to become a verified blogger a user must go through a verification process
controlled by platform admins. 

---

**Verification process** : 
1. User initiates Verification Request by sending image of their ID and an optional
message.
2. Verification Request is flagged with status ‘Verification requested’.
3. User can edit Verification Request until admin approves or declines it.
4. Admin can see list of all Verification Requests and filter them by user and status. Admin
can order them by date created.
5. Admin can approve Verification Request and user will be granted access for adding new
blog post. Verification Request is flagged with status ‘Approved’. User gets ‘Blogger’
role.
6. Admin can decline Verification Request and add the reason for rejection. Verification
Request is flagged with status ‘Declined’
7. User will get an email notifying them if their request was approved or declined

---

**Post** fields : 

* `title`
* `content`
* `date` (Auto-generated / Immutable)

**Users** main fields : 

* `email` 
* `firstname`
* `lastname`
* `password`
* `role` (ROLE_USER / ROLE_BLOGGER / ROLE_ADMIN)

**VerificationRequest** fields : 

* `status` (VERIFICATION_REQUESTED / VERIFICATION_APPROVED / VERIFICATION_DECLINED)
* `date` (Auto-generated / Immutable)
* `message`
* `rejectionReason`

---

# Development 

### Installation 

To install and run this app follow these instructions : 

- Clone the project ```$ git clone https://github.com/gdalyy/mini-blog-app-api.git```
- Install dependencies by running ```$ composer update``` under project directory
- Update your `.env` file according to your preferences
- Generate SSH keys in order to use [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#installation) and use the passphrase from your .env file

        $ mkdir -p config/jwt
        $ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
        $ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
- Create your database and migrate schema 

        $ php bin/console doctrine:database:create 
        $ php bin/console doctrine:migrations:migrate
        
- If you want to load data fixtures ```$ php bin/console doctrine:fixtures:load``` or simply run ```$ composer prepare```  
- And you're ready to GO ! launch local symfony web server ```$ symfony serve```          

API documentation is now available under `http://localhost:8000/api/docs` , enjoy :)

### Testing 
- copy the file `behat.xml.dist` to `behat.xml` 
- Update your `.env.test` file according to your preferences
- prepare testing environment by running ```$ composer prepare-test```
- run tests ```$ vendor/bin/behat```