<?php
/**
 * This bootstrap file is included to fix "symfony 5" & "FriendsOfBehat\SymfonyExtension"
 * incompatibility bug of reading .env.test variables
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
(new Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__, 2).'/.env');