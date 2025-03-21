-- SQL SCRIPT FOR THE DATABASE OF PANDAO CMS

-- **** Values to replace in the entire document ****
-- Database name:                   MY_DATABASE
-- Database user name:              MY_DB_USER
-- Database user password:          MY_DB_PASS
-- Admin user email:                USER_EMAIL
-- Admin user login:                USER_LOGIN
-- Admin user password:             USER_PASS_HASH
-- Installation date (timestamp):   INSTALL_DATE

-- **** Tables ****
-- solutionsCMS_user
-- solutionsCMS_lang
-- solutionsCMS_lang_file
-- solutionsCMS_page
-- solutionsCMS_page_file
-- solutionsCMS_menu
-- solutionsCMS_media
-- solutionsCMS_media_file
-- solutionsCMS_text
-- solutionsCMS_widget
-- solutionsCMS_widget_file
-- solutionsCMS_article
-- solutionsCMS_article_file
-- solutionsCMS_comment
-- solutionsCMS_tag
-- solutionsCMS_slide
-- solutionsCMS_slide_file
-- solutionsCMS_location
-- solutionsCMS_message
-- solutionsCMS_currency
-- solutionsCMS_country
-- solutionsCMS_social
-- solutionsCMS_email_content
-- solutionsCMS_popup

-- **** Edit with the name of your database ****
-- CREATE DATABASE IF NOT EXISTS `MY_DATABASE`;
-- USE `MY_DATABASE`;

-- **** Uncomment the following line if you are allowed to create users ****
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `MY_DATABASE`.* TO `MY_DB_USER`@`localhost` IDENTIFIED BY 'MY_DB_PASS' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

-- ================= CREATION OF THE TABLE solutionsCMS_user ===============

CREATE TABLE IF NOT EXISTS solutionsCMS_user(
    `id` int NOT NULL AUTO_INCREMENT,
    `firstname` varchar(100),
    `lastname` varchar(100),
    `email` varchar(100),
    `login` varchar(50),
    `pass` varchar(100),
    `type` varchar(20),
    `add_date` int,
    `edit_date` int,
    `checked` int DEFAULT 0,
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- **** Edit with the informations of the admin user ****
INSERT INTO `solutionsCMS_user` (`id`, `firstname`, `lastname`, `email`, `login`, `pass`, `type`, `add_date`, `edit_date`, `checked`) VALUES
(1, 'Administrator', '', 'USER_EMAIL', 'USER_LOGIN', 'USER_PASS_HASH', 'administrator', INSTALL_DATE, INSTALL_DATE, 1);

-- ================= CREATION OF THE TABLE solutionsCMS_lang ===============

CREATE TABLE IF NOT EXISTS solutionsCMS_lang(
    `id` int NOT NULL AUTO_INCREMENT,
    `title` varchar(20),
    `locale` varchar(20),
    `main` int DEFAULT 0,
    `checked` int DEFAULT 0,
    `rank` int DEFAULT 0,
    `tag` varchar(20),
    `rtl` int DEFAULT 0,
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

--
-- Content OF THE TABLE solutionsCMS_lang
--

INSERT INTO `solutionsCMS_lang` (`id`, `title`, `locale`, `main`, `checked`, `rank`, `tag`, `rtl`) VALUES
(1, 'Français', 'fr_FR', 0, 1, 2, 'fr', 0),
(2, 'English', 'en_GB', 1, 1, 1, 'en', 0);

-- ============== CREATION OF THE TABLE solutionsCMS_lang_file =============

CREATE TABLE IF NOT EXISTS solutionsCMS_lang_file (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_item` int NOT NULL,
    `home` int DEFAULT 0,
    `checked` int DEFAULT 1,
    `rank` int DEFAULT 0,
    `file` varchar(250),
    `label` varchar(250),
    `type` varchar(20),
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_lang_file ADD CONSTRAINT lang_file_fkey FOREIGN KEY (id_item) REFERENCES solutionsCMS_lang(id) ON UPDATE NO ACTION ON DELETE CASCADE;

--
-- Content OF THE TABLE solutionsCMS_lang_file
--

INSERT INTO `solutionsCMS_lang_file` (`id`, `id_item`, `home`, `checked`, `rank`, `file`, `label`, `type`) VALUES
(1, 1, 0, 1, 2, 'fr.png', '', 'image'),
(2, 2, 0, 1, 1, 'gb.png', '', 'image');

-- ================= CREATION OF THE TABLE solutionsCMS_page ===============

CREATE TABLE IF NOT EXISTS solutionsCMS_page (
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `name` varchar(50),
    `title` varchar(250),
    `subtitle` varchar(250),
    `title_tag` varchar(250),
    `alias` varchar(100),
    `descr` longtext,
    `robots` varchar(20),
    `keywords` varchar(250),
    `text` longtext,
    `id_parent` int,
    `page_model` varchar(50),
    `article_model` varchar(50),
    `home` int DEFAULT 0,
    `checked` int DEFAULT 0,
    `rank` int DEFAULT 0,
    `add_date` int,
    `edit_date` int,
    `comment` int DEFAULT 0,
    `rating` int DEFAULT 0,
    `system` int DEFAULT 0,
    `show_langs` text,
    `hide_langs` text,
    PRIMARY KEY(id, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_page ADD CONSTRAINT page_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content OF THE TABLE solutionsCMS_page
--

INSERT INTO `solutionsCMS_page` (`id`, `lang`, `name`, `title`, `subtitle`, `title_tag`, `alias`, `descr`, `robots`, `keywords`, `text`, `id_parent`, `page_model`, `article_model`, `home`, `checked`, `rank`, `add_date`, `edit_date`, `comment`, `rating`, `system`, `show_langs`, `hide_langs`) VALUES
(1, 1, 'Accueil', 'Lorem ipsum dolor sit amet', 'Consectetur adipiscing elit', 'Accueil', '', '', 'index,follow', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse dictum mi quis lacus iaculis, laoreet auctor augue sagittis. Pellentesque at dignissim ex, sit amet lobortis risus. In auctor dictum ligula a elementum. Nam porttitor quam sit amet ultrices sollicitudin. Morbi tortor lectus, laoreet a augue a, viverra sagittis erat. Maecenas suscipit felis turpis, et vestibulum tortor ultrices ac. Maecenas varius quis dui vitae malesuada. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec et turpis porttitor, rutrum arcu a, molestie nulla. Vivamus semper nunc at viverra porttitor. Quisque tincidunt, nunc nec faucibus mattis, ligula magna pharetra dolor, maximus sodales mi augue vel mi. Vivamus quis placerat quam. Nulla vel massa eu felis dapibus maximus in eget risus. Aliquam erat volutpat. Maecenas sem neque, consequat sit amet nibh in, facilisis cursus ante.</p>', NULL, 'home', '', 1, 1, 1, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL),
(1, 2, 'Home', 'Create and manage your own website', 'Solutions CMS', 'Solutions CMS web software to create and manage your own website', '', '', 'index,follow', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse dictum mi quis lacus iaculis, laoreet auctor augue sagittis. Pellentesque at dignissim ex, sit amet lobortis risus. In auctor dictum ligula a elementum. Nam porttitor quam sit amet ultrices sollicitudin. Morbi tortor lectus, laoreet a augue a, viverra sagittis erat. Maecenas suscipit felis turpis, et vestibulum tortor ultrices ac. Maecenas varius quis dui vitae malesuada. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec et turpis porttitor, rutrum arcu a, molestie nulla. Vivamus semper nunc at viverra porttitor. Quisque tincidunt, nunc nec faucibus mattis, ligula magna pharetra dolor, maximus sodales mi augue vel mi. Vivamus quis placerat quam. Nulla vel massa eu felis dapibus maximus in eget risus. Aliquam erat volutpat. Maecenas sem neque, consequat sit amet nibh in, facilisis cursus ante.</p>', NULL, 'home', '', 1, 1, 1, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL),
(2, 1, 'Contact', 'Contact', '', 'Contact', 'contact', '', 'index,follow', '', '<h2 style=\"text-align: center;\">Restons en contact</h2>\r\n', NULL, 'contact', '', 0, 1, 4, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL),
(2, 2, 'Contact', 'Contact', '', 'Contact', 'contact', '', 'index,follow', '', '<h2 style=\"text-align: center;\">Get in touch with us</h2>\r\n', NULL, 'contact', '', 0, 1, 4, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL),
(3, 1, 'Mentions légales', 'Mentions légales', '', 'Mentions légales', 'mentions-legales', '', 'index,follow', '', '', NULL, 'page', '', 0, 1, 5, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL),
(3, 2, 'Legal notices', 'Legal notices', '', 'Legal notices', 'legal-notices', '', 'index,follow', '', '', NULL, 'page', '', 0, 1, 5, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL),
(4, 1, 'Ma première page', 'Ma première page', '', 'Ma première page', 'my-first-page', '', 'index,follow', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque fringilla vel est at rhoncus. Cras porttitor ligula vel magna vehicula accumsan. Mauris eget elit et sem commodo interdum. Aenean dolor sem, tincidunt ac neque tempus, hendrerit blandit lacus. Vivamus placerat nulla in mi tristique, fringilla fermentum nisl vehicula. Nullam quis eros non magna tincidunt interdum ac eu eros. Morbi malesuada pulvinar ultrices. Etiam bibendum efficitur risus, sit amet venenatis urna ullamcorper non. Proin fermentum malesuada tortor, vitae mattis sem scelerisque in. Curabitur rutrum leo at mi efficitur suscipit. Vivamus tristique lorem eros, sit amet malesuada augue sodales sed.</p>', NULL, 'page', 'article', 0, 1, 2, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL),
(4, 2, 'My first page', 'My first page', '', 'My first page', 'my-first-page', '', 'index,follow', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque fringilla vel est at rhoncus. Cras porttitor ligula vel magna vehicula accumsan. Mauris eget elit et sem commodo interdum. Aenean dolor sem, tincidunt ac neque tempus, hendrerit blandit lacus. Vivamus placerat nulla in mi tristique, fringilla fermentum nisl vehicula. Nullam quis eros non magna tincidunt interdum ac eu eros. Morbi malesuada pulvinar ultrices. Etiam bibendum efficitur risus, sit amet venenatis urna ullamcorper non. Proin fermentum malesuada tortor, vitae mattis sem scelerisque in. Curabitur rutrum leo at mi efficitur suscipit. Vivamus tristique lorem eros, sit amet malesuada augue sodales sed.</p>\n', NULL, 'page', 'article', 0, 1, 2, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL),
(5, 1, 'Recherche', 'Recherche', '', 'Recherche', 'search', '', 'noindex,nofollow', '', '', NULL, 'search', '', 0, 1, 6, INSTALL_DATE, INSTALL_DATE, 0, NULL, 1, NULL, NULL),
(5, 2, 'Search', 'Search', '', 'Search', 'search', '', 'noindex,nofollow', '', '', NULL, 'search', '', 0, 1, 6, INSTALL_DATE, INSTALL_DATE, 0, NULL, 1, NULL, NULL),
(6, 1, '404', 'Erreur 404 : Page introuvable !', '', '404 Page introuvable', '404', '', 'noindex,nofollow', '', '<p>L\'URL demandée n\'a pas été trouvée sur ce serveur.<br />\r\nLa page que vous voulez afficher n\'existe pas, ou est temporairement indisponible.</p>\r\n\r\n<p>Merci d\'essayer les actions suivantes :</p>\r\n\r\n<ul>\r\n	<li>Assurez-vous que l\'URL dans la barre d\'adresse de votre navigateur est correctement orthographiée et formatée.</li>\r\n	<li>Si vous avez atteint cette page en cliquant sur un lien ou si vous pensez que cela concerne une erreur du serveur, contactez l\'administrateur pour l\'alerter.</li>\r\n</ul>\r\n', NULL, '404', '', 0, 1, 7, INSTALL_DATE, INSTALL_DATE, 0, NULL, 1, NULL, NULL),
(6, 2, '404', '404 Error: Page not found!', '', '404 Not Found', '404', '', 'noindex,nofollow', '', '<p>The wanted URL was not found on this server.<br />\r\nThe page you wish to display does not exist, or is temporarily unavailable.</p>\r\n\r\n<p>Thank you for trying the following actions :</p>\r\n\r\n<ul>\r\n	<li>Be sure the URL in the address bar of your browser is correctly spelt and formated.</li>\r\n	<li>If you reached this page by clicking a link or if you think that it is about an error of the server, contact the administrator to alert him.</li>\r\n</ul>\r\n', NULL, '404', '', 0, 1, 7, INSTALL_DATE, INSTALL_DATE, 0, NULL, 1, NULL, NULL),
(7, 1, 'Blog', 'Blog', '', 'Blog', 'blog', '', 'index,follow', '', '', NULL, 'blog', 'article-blog', 0, 1, 3, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL),
(7, 2, 'Blog', 'Blog', '', 'Blog', 'blog', '', 'index,follow', '', '', NULL, 'blog', 'article-blog', 0, 1, 3, INSTALL_DATE, INSTALL_DATE, 0, NULL, 0, NULL, NULL);

-- ============== CREATION OF THE TABLE solutionsCMS_page_file =============

CREATE TABLE IF NOT EXISTS solutionsCMS_page_file (
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `id_item` int NOT NULL,
    `home` int DEFAULT 0,
    `checked` int DEFAULT 1,
    `rank` int DEFAULT 0,
    `file` varchar(250),
    `label` varchar(250),
    `type` varchar(20),
    PRIMARY KEY(id,lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_page_file ADD CONSTRAINT page_file_fkey FOREIGN KEY (id_item, lang) REFERENCES solutionsCMS_page(id, lang) ON UPDATE NO ACTION ON DELETE CASCADE;
ALTER TABLE solutionsCMS_page_file ADD CONSTRAINT page_file_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

-- ================= CREATION OF THE TABLE solutionsCMS_menu ===============

CREATE TABLE IF NOT EXISTS solutionsCMS_menu(
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `name` varchar(50),
    `title` varchar(250),
    `id_parent` int,
    `item_type` varchar(30),
    `id_item` int,
    `url` text,
    `main` int DEFAULT 1,
    `footer` int DEFAULT 0,
    `checked` int DEFAULT 0,
    `rank` int DEFAULT 0,
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_menu ADD CONSTRAINT menu_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content OF THE TABLE solutionsCMS_menu
--

INSERT INTO `solutionsCMS_menu` (`id`, `lang`, `name`, `title`, `id_parent`, `item_type`, `id_item`, `url`, `main`, `footer`, `checked`, `rank`) VALUES
(1, 1, 'Accueil', '', NULL, 'page', 1, '', 1, 0, 1, 1),
(1, 2, 'Home', '', NULL, 'page', 1, '', 1, 0, 1, 1),
(2, 1, 'Contact', '', NULL, 'page', 2, '', 1, 1, 1, 4),
(2, 2, 'Contact', '', NULL, 'page', 2, '', 1, 1, 1, 4),
(3, 1, 'Mentions légales', '', NULL, 'page', 3, '', 0, 1, 1, 5),
(3, 2, 'Legal notices', '', NULL, 'page', 3, '', 0, 1, 1, 5),
(4, 1, 'Ma première page', '', NULL, 'page', 5, '', 1, 0, 1, 2),
(4, 2, 'My first page', '', NULL, 'page', 5, '', 1, 0, 1, 2);

-- ================ CREATION OF THE TABLE solutionsCMS_media ===============

CREATE TABLE IF NOT EXISTS solutionsCMS_media(
    `id` int NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- ============== CREATION OF THE TABLE solutionsCMS_media_file ============

CREATE TABLE IF NOT EXISTS solutionsCMS_media_file (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_item` int NOT NULL,
    `home` int DEFAULT 0,
    `checked` int DEFAULT 1,
    `rank` int DEFAULT 0,
    `file` varchar(250),
    `label` varchar(250),
    `type` varchar(20),
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_media_file ADD CONSTRAINT media_file_fkey FOREIGN KEY (id_item) REFERENCES solutionsCMS_media(id) ON UPDATE NO ACTION ON DELETE CASCADE;

-- ================ CREATION OF THE TABLE solutionsCMS_text ================

CREATE TABLE IF NOT EXISTS solutionsCMS_text(
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `name` varchar(50),
    `value` text,
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_text ADD CONSTRAINT text_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content OF THE TABLE solutionsCMS_text
--

INSERT INTO `solutionsCMS_text` (`id`, `lang`, `name`, `value`) VALUES
(1, 1, 'CREATION', 'Création'),
(1, 2, 'CREATION', 'Creation'),
(2, 1, 'MESSAGE', 'Message'),
(2, 2, 'MESSAGE', 'Message'),
(3, 1, 'EMAIL', 'E-mail'),
(3, 2, 'EMAIL', 'E-mail'),
(4, 1, 'PHONE', 'Tél.'),
(4, 2, 'PHONE', 'Phone'),
(5, 1, 'FAX', 'Fax'),
(5, 2, 'FAX', 'Fax'),
(6, 1, 'COMPANY', 'Société'),
(6, 2, 'COMPANY', 'Company'),
(7, 1, 'COPY_CODE', 'Recopiez le code'),
(7, 2, 'COPY_CODE', 'Copy the code'),
(8, 1, 'SUBJECT', 'Sujet'),
(8, 2, 'SUBJECT', 'Subject'),
(9, 1, 'REQUIRED_FIELD', 'Champ requis'),
(9, 2, 'REQUIRED_FIELD', 'Required field'),
(10, 1, 'INVALID_CAPTCHA_CODE', 'Le code de sécurité saisi est incorrect'),
(10, 2, 'INVALID_CAPTCHA_CODE', 'Invalid security code'),
(11, 1, 'INVALID_EMAIL', 'Adresse e-mail invalide'),
(11, 2, 'INVALID_EMAIL', 'Invalid email address'),
(12, 1, 'FIRSTNAME', 'Prénom'),
(12, 2, 'FIRSTNAME', 'Firstname'),
(13, 1, 'LASTNAME', 'Nom'),
(13, 2, 'LASTNAME', 'Lastname'),
(14, 1, 'ADDRESS', 'Adresse'),
(14, 2, 'ADDRESS', 'Address'),
(15, 1, 'POSTCODE', 'Code postal'),
(15, 2, 'POSTCODE', 'Post code'),
(16, 1, 'CITY', 'Ville'),
(16, 2, 'CITY', 'City'),
(17, 1, 'MOBILE', 'Portable'),
(17, 2, 'MOBILE', 'Mobile'),
(18, 1, 'ADD', 'Ajouter'),
(18, 2, 'ADD', 'Add'),
(19, 1, 'EDIT', 'Modifier'),
(19, 2, 'EDIT', 'Edit'),
(20, 1, 'INVALID_INPUT', 'Saisie invalide'),
(20, 2, 'INVALID_INPUT', 'Invalid input'),
(21, 1, 'MAIL_DELIVERY_FAILURE', 'Echec lors de l''envoi du message.'),
(21, 2, 'MAIL_DELIVERY_FAILURE', 'A failure occurred during the delivery of this message.'),
(22, 1, 'MAIL_DELIVERY_SUCCESS', 'Merci de votre intérêt, votre message a bien été envoyé.\nNous vous contacterons dans les plus brefs délais.'),
(22, 2, 'MAIL_DELIVERY_SUCCESS', 'Thank you for your interest, your message has been sent.\nWe will contact you as soon as possible.'),
(23, 1, 'SEND', 'Envoyer'),
(23, 2, 'SEND', 'Send'),
(24, 1, 'FORM_ERRORS', 'Le formulaire comporte des erreurs.'),
(24, 2, 'FORM_ERRORS', 'The following form contains some errors.'),
(25, 1, 'FROM_DATE', 'Du'),
(25, 2, 'FROM_DATE', 'From'),
(26, 1, 'TO_DATE', 'au'),
(26, 2, 'TO_DATE', 'till'),
(27, 1, 'FROM', 'De'),
(27, 2, 'FROM', 'From'),
(28, 1, 'TO', 'à'),
(28, 2, 'TO', 'to'),
(29, 1, 'BOOK', 'Réserver'),
(29, 2, 'BOOK', 'Book'),
(30, 1, 'READMORE', 'Lire la suite'),
(30, 2, 'READMORE', 'Read more'),
(31, 1, 'BACK', 'Retour'),
(31, 2, 'BACK', 'Back'),
(32, 1, 'DISCOVER', 'Découvrir'),
(32, 2, 'DISCOVER', 'Discover'),
(33, 1, 'ALL', 'Tous'),
(33, 2, 'ALL', 'All'),
(34, 1, 'ALL_RIGHTS_RESERVED', 'Tous droits réservés'),
(34, 2, 'ALL_RIGHTS_RESERVED', 'All rights reserved'),
(35, 1, 'FORGOTTEN_PASSWORD', 'Mot de passe oublié ?'),
(35, 2, 'FORGOTTEN_PASSWORD', 'Forgotten password?'),
(36, 1, 'LOG_IN', 'Connexion'),
(36, 2, 'LOG_IN', 'Log in'),
(37, 1, 'SIGN_UP', 'Inscription'),
(37, 2, 'SIGN_UP', 'Sign up'),
(38, 1, 'LOG_OUT', 'Déconnexion'),
(38, 2, 'LOG_OUT', 'Log out'),
(39, 1, 'SEARCH', 'Rechercher'),
(39, 2, 'SEARCH', 'Search'),
(40, 1, 'RESET_PASS_SUCCESS', 'Votre nouveau mot de passe vous a été envoyé sur votre adresse e-mail.'),
(40, 2, 'RESET_PASS_SUCCESS', 'Your new password was sent to you on your e-mail.'),
(41, 1, 'PASS_TOO_SHORT', 'Le mot de passe doit contenir 6 caractères au minimum'),
(41, 2, 'PASS_TOO_SHORT', 'The password must contain 6 characters at least'),
(42, 1, 'PASS_DONT_MATCH', 'Les mots de passe doivent correspondre'),
(42, 2, 'PASS_DONT_MATCH', 'The passwords don''t match'),
(43, 1, 'ACCOUNT_EXISTS', 'Un compte existe déjà avec cette adresse e-mail'),
(43, 2, 'ACCOUNT_EXISTS', 'An account already exists with this e-mail'),
(44, 1, 'ACCOUNT_CREATED', 'Votre compte a bien été créé.'),
(44, 2, 'ACCOUNT_CREATED', 'Your account was well created.'),
(45, 1, 'INCORRECT_LOGIN', 'Les informations de connexion sont incorrectes.'),
(45, 2, 'INCORRECT_LOGIN', 'Incorrect login information.'),
(46, 1, 'I_SIGN_UP', 'Je m''inscris'),
(46, 2, 'I_SIGN_UP', 'I sign up'),
(47, 1, 'ALREADY_HAVE_ACCOUNT', 'J''ai déjà un compte'),
(47, 2, 'ALREADY_HAVE_ACCOUNT', 'I already have an account'),
(48, 1, 'MY_ACCOUNT', 'Mon compte'),
(48, 2, 'MY_ACCOUNT', 'My account'),
(49, 1, 'COMMENTS', 'Commentaires'),
(49, 2, 'COMMENTS', 'Comments'),
(50, 1, 'LET_US_KNOW', 'Faîtes-nous savoir ce que vous pensez'),
(50, 2, 'LET_US_KNOW', 'Let us know what you think'),
(51, 1, 'COMMENT_SUCCESS', 'Merci de votre intérêt, votre commentaire va être soumis à validation.'),
(51, 2, 'COMMENT_SUCCESS', 'Thank you for your interest, your comment will be checked.'),
(52, 1, 'NO_SEARCH_RESULT', 'Aucun résultat. Vérifiez l''orthographe des termes de recherche (> 3 caractères) ou essayez d''autres mots.'),
(52, 2, 'NO_SEARCH_RESULT', 'No result. Check the spelling terms of search (> 3 characters) or try other words.'),
(53, 1, 'SEARCH_EXCEEDED', 'Nombre de recherches dépassé.'),
(53, 2, 'SEARCH_EXCEEDED', 'Number of researches exceeded.'),
(54, 1, 'SECONDS', 'secondes'),
(54, 2, 'SECONDS', 'seconds'),
(55, 1, 'FOR_A_TOTAL_OF', 'sur un total de'),
(55, 2, 'FOR_A_TOTAL_OF', 'for a total of'),
(56, 1, 'COMMENT', 'Commentaire'),
(56, 2, 'COMMENT', 'Comment'),
(57, 1, 'VIEW', 'Visionner'),
(57, 2, 'VIEW', 'View'),
(58, 1, 'RECENT_ARTICLES', 'Articles récents'),
(58, 2, 'RECENT_ARTICLES', 'Recent articles'),
(59, 1, 'RSS_FEED', 'Flux RSS'),
(59, 2, 'RSS_FEED', 'RSS feed'),
(60, 1, 'RATINGS', 'Note(s)'),
(60, 2, 'RATINGS', 'Rating(s)'),
(61, 1, 'COOKIES_NOTICE', 'Les cookies nous aident à fournir une meilleure expérience utilisateur. En utilisant notre site, vous acceptez l''utilisation de cookies.'),
(61, 2, 'COOKIES_NOTICE', 'Cookies help us provide better user experience. By using our website, you agree to the use of cookies.'),
(62, 1, 'RESULTS', 'Résultats'),
(62, 2, 'RESULTS', 'Results'),
(63, 1, 'TAGS', 'Tags'),
(63, 2, 'TAGS', 'Tags'),
(64, 1, 'ARCHIVES', 'Archives'),
(64, 2, 'ARCHIVES', 'Archives'),
(65, 1, 'LOAD_MORE', 'Voir plus'),
(65, 2, 'LOAD_MORE', 'Load more'),
(66, 1, 'PRIVACY_POLICY_AGREEMENT', '<small>J''accepte que les informations recueillies par ce formulaire soient stockées dans un fichier informatisé afin de traiter ma demande.<br>Conformément au "Réglement Général sur la Protection des Données", vous pouvez exercer votre droit d''accès aux données vous concernant et les faire rectifier via le formulaire de contact.</small>'),
(66, 2, 'PRIVACY_POLICY_AGREEMENT', '<small>I agree that the information collected by this form will be stored in a database in order to process my request.<br>In accordance with the "General Data Protection Regulation", you can exercise your right to access to your data and make them rectified via the contact form.</small>'),
(67, 1, 'DISCOVER_ALSO', 'Découvrez aussi'),
(67, 2, 'DISCOVER_ALSO', 'Discover also'),
(68, 1, 'GET_IN_TOUCH', 'Restons en contact'),
(68, 2, 'GET_IN_TOUCH', 'Get in touch'),
(69, 1, 'FOLLOW_US', 'Suivez-nous'),
(69, 2, 'FOLLOW_US', 'Follow us'),
(70, 1, 'WHATS_NEW', 'Quoi de neuf ?'),
(70, 2, 'WHATS_NEW', 'What''s new?'),
(71, 1, 'SHARE', 'Partager'),
(71, 2, 'SHARE', 'Share'),
(72, 1, 'PREV', 'Previous'),
(72, 2, 'PREV', 'Précédent'),
(73, 1, 'NEXT', 'Previous'),
(73, 2, 'NEXT', 'Précédent');

-- =============== CREATION OF THE TABLE solutionsCMS_widget ===============

CREATE TABLE IF NOT EXISTS solutionsCMS_widget(
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `title` varchar(250),
    `subtitle` varchar(250),
    `showtitle` int,
    `pos` varchar(20),
    `allpages` int,
    `pages` varchar(250),
    `type` varchar(20),
    `class` varchar(250),
    `content` longtext,
    `checked` int DEFAULT 0,
    `rank` int DEFAULT 0,
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_widget ADD CONSTRAINT widget_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content OF THE TABLE solutionsCMS_widget
--

INSERT INTO `solutionsCMS_widget` (`id`, `lang`, `title`, `subtitle`, `showtitle`, `pos`, `allpages`, `pages`, `type`, `class`, `content`, `checked`, `rank`) VALUES
(1, 1, 'Qui sommes-nous ?', NULL, 1, 'footer_col_3', 1, '', '', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget auctor ipsum. Mauris pharetra neque a mauris commodo, at aliquam leo malesuada. Maecenas eget elit eu ligula rhoncus dapibus at non erat. In sed velit eget eros gravida consectetur varius imperdiet lectus.</p>', 1, 13),
(1, 2, 'About us', NULL, 1, 'footer_col_3', 1, '', '', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget auctor ipsum. Mauris pharetra neque a mauris commodo, at aliquam leo malesuada. Maecenas eget elit eu ligula rhoncus dapibus at non erat. In sed velit eget eros gravida consectetur varius imperdiet lectus.</p>', 1, 13),
(3, 1, 'Derniers articles', '', 1, 'main_before', 0, '1', 'check_list', '', '<h4><i class=\"fa-regular fa-heart\"></i> Lorem ipsum</h4><p>Sed nisl ante, gravida ut diam sed, vestibulum dictum massa. Proin suscipit dolor eget odio egestas, ac sodales erat mollis.</p><hr><h4><i class=\"fa-regular fa-gem\"></i> Lorem ipsum</h4><p>Sed nisl ante, gravida ut diam sed, vestibulum dictum massa. Proin suscipit dolor eget odio egestas, ac sodales erat mollis.</p><hr><h4><i class=\"fa-regular fa-star\"></i> Lorem ipsum</h4><p>Sed nisl ante, gravida ut diam sed, vestibulum dictum massa. Proin suscipit dolor eget odio egestas, ac sodales erat mollis.</p><hr><h4><i class=\"fa-regular fa-paper-plane\"></i> Lorem ipsum</h4><p>Sed nisl ante, gravida ut diam sed, vestibulum dictum massa. Proin suscipit dolor eget odio egestas, ac sodales erat mollis.</p>', 1, 1),
(3, 2, 'What solutions we offer you', 'Our services', 1, 'main_before', 0, '1', 'check_list', '', '<h4><i class=\"fa-regular fa-heart\"></i> Lorem ipsum</h4><p>Sed nisl ante, gravida ut diam sed, vestibulum dictum massa. Proin suscipit dolor eget odio egestas, ac sodales erat mollis.</p><hr><h4><i class=\"fa-regular fa-gem\"></i> Lorem ipsum</h4><p>Sed nisl ante, gravida ut diam sed, vestibulum dictum massa. Proin suscipit dolor eget odio egestas, ac sodales erat mollis.</p><hr><h4><i class=\"fa-regular fa-star\"></i> Lorem ipsum</h4><p>Sed nisl ante, gravida ut diam sed, vestibulum dictum massa. Proin suscipit dolor eget odio egestas, ac sodales erat mollis.</p><hr><h4><i class=\"fa-regular fa-paper-plane\"></i> Lorem ipsum</h4><p>Sed nisl ante, gravida ut diam sed, vestibulum dictum massa. Proin suscipit dolor eget odio egestas, ac sodales erat mollis.</p>', 1, 1),
(4, 1, 'Contactez-nous', NULL, 0, 'footer_col_1', 1, '', 'contact_informations', '', '', 1, 11),
(4, 2, 'Get in touch', NULL, 0, 'footer_col_1', 1, '', 'contact_informations', '', '', 1, 11),
(5, 1, 'Blog side', NULL, 0, 'right', 0, '8', 'blog_side', '', '', 1, 10),
(5, 2, 'Blog side', NULL, 0, 'right', 0, '8', 'blog_side', '', '', 1, 10),
(6, 1, 'Navigation', NULL, 1, 'footer_col_2', 1, '', 'footer_menu', '', '', 1, 12),
(6, 2, 'Navigation', NULL, 1, 'footer_col_2', 1, '', 'footer_menu', '', '', 1, 12),
(7, 1, 'Articles pages', NULL, 0, 'main_after', 1, '', 'articles_grid', '', '', 1, 8),
(7, 2, 'Articles pages', NULL, 0, 'main_after', 1, '', 'articles_grid', '', '', 1, 8),
(8, 1, 'Article home', NULL, 0, 'main_before', 0, '1', 'featured_home_single', '', '', 1, 2),
(8, 2, 'Article home', NULL, 0, 'main_before', 0, '1', 'featured_home_single', '', '', 1, 2),
(9, 1, 'Articles home', NULL, 0, 'main_before', 0, '1', 'featured_home', '', '', 1, 4),
(9, 2, 'Article home', NULL, 0, 'main_before', 0, '1', 'featured_home', '', '', 1, 4),
(10, 1, 'Parallax home', NULL, 0, 'main_after', 0, '1', 'parallax_home', '', '<h2 style=\"text-align:center;\">Your content here...</h2><p style=\"text-align:center;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse dictum mi quis lacus iaculis, laoreet auctor augue sagittis. Pellentesque at dignissim ex, sit amet lobortis risus. In auctor dictum ligula a elementum. Nam porttitor quam sit amet ultrices sollicitudin.</p><p style=\"text-align:center;\"><a class=\"btn btn-primary\" href=\"#\">Contact us</a></p>', 1, 9),
(10, 2, 'Parallax background', NULL, 0, 'main_after', 0, '1', 'parallax_home', '', '<h2 style=\"text-align:center;\">Your content here...</h2><p style=\"text-align:center;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse dictum mi quis lacus iaculis, laoreet auctor augue sagittis. Pellentesque at dignissim ex, sit amet lobortis risus. In auctor dictum ligula a elementum. Nam porttitor quam sit amet ultrices sollicitudin.</p><p style=\"text-align:center;\"><a class=\"btn btn-primary\" href=\"#\">Contact us</a></p>', 1, 9),
(12, 1, 'Témoignages', NULL, 0, 'main_before', 0, '1', 'testimonials', '', '', 1, 6),
(12, 2, 'Testimonials', NULL, 0, 'main_before', 0, '1', 'testimonials', '', '', 1, 6),
(13, 1, 'Video Background', NULL, 0, 'main_before', 0, '1', 'video_background', '', '<h2>Video Background</h2><p>Video background is a great way to attract your visitors and make them stay longer on your site.</p>', 1, 7),
(13, 2, 'Video Background', NULL, 0, 'main_before', 0, '1', 'video_background', '', '<h2>Video Background</h2><p>Video background is a great way to attract your visitors and make them stay longer on your site.</p>', 1, 7),
(14, 1, 'Parallax home', NULL, 0, 'main_before', 0, '1', 'banner_full_width', '', '<h2 style=\"text-align:center;\">Your content here...</h2><p style=\"text-align:center;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse dictum mi quis lacus iaculis, laoreet auctor augue sagittis. Pellentesque at dignissim ex, sit amet lobortis risus. In auctor dictum ligula a elementum. Nam porttitor quam sit amet ultrices sollicitudin.</p><p style=\"text-align:center;\"><a class=\"btn btn-primary\" href=\"#\">Contact us</a></p>', 1, 5),
(14, 2, 'Banner full width', NULL, 0, 'main_before', 0, '1', 'banner_full_width', '', '<h2 style=\"text-align:center;\">Your content here...</h2><p style=\"text-align:center;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse dictum mi quis lacus iaculis, laoreet auctor augue sagittis. Pellentesque at dignissim ex, sit amet lobortis risus. In auctor dictum ligula a elementum. Nam porttitor quam sit amet ultrices sollicitudin.</p><p style=\"text-align:center;\"><a class=\"btn btn-primary\" href=\"#\">Contact us</a></p>', 1, 5),
(15, 1, 'Banner Ad', NULL, 0, 'main_before', 0, '1', '', '', '<figure class=\"image\"><a href=\"#\"><img style=\"aspect-ratio:1320/413;\" src=\"medias/uploads/66ab91d3d587b/banner-min.png\" width=\"1320\" height=\"413\"></a></figure>', 1, 3),
(15, 2, 'Banner Ad', NULL, 0, 'main_before', 0, '1', '', '', '<figure class=\"image\"><a href=\"#\"><img style=\"aspect-ratio:1320/413;\" src=\"medias/uploads/66ab91d3d587b/banner-min.png\" width=\"1320\" height=\"413\"></a></figure>', 1, 3);

-- ============= CREATION OF THE TABLE solutionsCMS_widget_file ===========

CREATE TABLE IF NOT EXISTS solutionsCMS_widget_file (
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `id_item` int NOT NULL,
    `home` int DEFAULT 0,
    `checked` int DEFAULT 1,
    `rank` int DEFAULT 0,
    `file` varchar(250),
    `label` varchar(250),
    `type` varchar(20),
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_widget_file ADD CONSTRAINT widget_file_fkey FOREIGN KEY (id_item, lang) REFERENCES solutionsCMS_widget(id, lang) ON UPDATE NO ACTION ON DELETE CASCADE;
ALTER TABLE solutionsCMS_widget_file ADD CONSTRAINT widget_file_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content OF THE TABLE solutionsCMS_widget_file
--

INSERT INTO `solutionsCMS_widget_file` (`id`, `lang`, `id_item`, `home`, `checked`, `rank`, `file`, `label`, `type`) VALUES
(1, 1, 13, 0, 1, 1, 'video.mp4', '', 'other'),
(1, 2, 13, 0, 1, 1, 'video.mp4', '', 'other'),
(2, 1, 10, 0, 1, 2, 'bg-parallax.jpg', '', 'image'),
(2, 2, 10, 0, 1, 2, 'bg-parallax.jpg', '', 'image'),
(3, 1, 14, 0, 1, 1, 'banner.png', '', 'image'),
(3, 2, 14, 0, 1, 1, 'banner.png', '', 'image');

-- ================ CREATION OF THE TABLE solutionsCMS_article =============

CREATE TABLE IF NOT EXISTS solutionsCMS_article(
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `title` varchar(250),
    `subtitle` varchar(250),
    `alias` varchar(100),
    `short_text` text,
    `text` longtext,
    `url` varchar(250),
    `tags` varchar(250),
    `id_page` int,
    `users` text,
    `home` int DEFAULT 0,
    `checked` int DEFAULT 0,
    `rank` int DEFAULT 0,
    `add_date` int,
    `edit_date` int,
    `publish_date` int,
    `unpublish_date` int,
    `comment` int DEFAULT 0,
    `rating` int DEFAULT 0,
    `show_langs` text,
    `hide_langs` text,
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_article ADD CONSTRAINT article_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE solutionsCMS_article ADD CONSTRAINT article_page_fkey FOREIGN KEY (id_page, lang) REFERENCES solutionsCMS_page(id, lang) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content OF THE TABLE solutionsCMS_article
--

INSERT INTO `solutionsCMS_article` (`id`, `lang`, `title`, `subtitle`, `alias`, `short_text`, `text`, `url`, `tags`, `id_page`, `users`, `home`, `checked`, `rank`, `add_date`, `edit_date`, `publish_date`, `unpublish_date`, `comment`, `rating`, `show_langs`, `hide_langs`) VALUES
(1, 1, 'Mon premier article', '', 'mon-premier-article', '', '<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque.</p><ul><li>Brand Identity</li><li>Website Design & Development</li><li>SEO & Analytics</li></ul>', '', '', 4, '1', 1, 1, 1, INSTALL_DATE, INSTALL_DATE, NULL, NULL, 1, NULL, '', ''),
(1, 2, 'My first article', '', 'my-first-article', '', '<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque.</p><ul><li>Brand Identity</li><li>Website Design & Development</li><li>SEO & Analytics</li></ul>', '', '', 4, '1', 1, 1, 1, INSTALL_DATE, INSTALL_DATE, NULL, NULL, 1, NULL, '', ''),
(2, 1, 'Mon premier article', '', 'mon-premier-article', '', '<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque. Donec et faucibus justo. Nulla vitae nisl lacus. Fusce tincidunt quam nec vestibulum vestibulum. Vivamus vulputate, nunc non ullamcorper mattis, nunc orci imperdiet nulla, at laoreet ipsum nisl non leo. Aenean dapibus aliquet sem, ut lacinia magna mattis in.</p><h3>Mauris et euismod enim, eget elementum orci</h3><p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur tempor arcu eu sapien ullamcorper sodales. Aenean eu massa in ante commodo scelerisque vitae sed sapien. Aenean eu dictum arcu. Mauris ultricies dolor eu molestie egestas.<br>Proin feugiat, nunc at pellentesque fringilla, ex purus efficitur dolor, ac pretium odio lacus id leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse eu ipsum viverra dolor tempus vehicula eu eu risus. Praesent rutrum dapibus odio, nec accumsan justo fermentum in. Ut quis neque a ante facilisis bibendum.</p>', '', '', 4, '1', 1, 1, 2, INSTALL_DATE, INSTALL_DATE, NULL, NULL, 1, NULL, '', ''),
(2, 2, 'Lorem ipsum dolor sit amet consectetur ', '', 'lorem-ipsum-dolor-sit-amet-consectetur', 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar.', '<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque. Donec et faucibus justo. Nulla vitae nisl lacus. Fusce tincidunt quam nec vestibulum vestibulum. Vivamus vulputate, nunc non ullamcorper mattis, nunc orci imperdiet nulla, at laoreet ipsum nisl non leo. Aenean dapibus aliquet sem, ut lacinia magna mattis in.</p><h3>Mauris et euismod enim, eget elementum orci</h3><p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur tempor arcu eu sapien ullamcorper sodales. Aenean eu massa in ante commodo scelerisque vitae sed sapien. Aenean eu dictum arcu. Mauris ultricies dolor eu molestie egestas.<br>Proin feugiat, nunc at pellentesque fringilla, ex purus efficitur dolor, ac pretium odio lacus id leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse eu ipsum viverra dolor tempus vehicula eu eu risus. Praesent rutrum dapibus odio, nec accumsan justo fermentum in. Ut quis neque a ante facilisis bibendum.</p>', '', '', 4, '1', 1, 1, 2, INSTALL_DATE, INSTALL_DATE, NULL, NULL, 1, NULL, '', ''),
(3, 1, 'Mon premier article', '', 'mon-premier-article', 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. ', '<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque. Donec et faucibus justo. Nulla vitae nisl lacus. Fusce tincidunt quam nec vestibulum vestibulum. Vivamus vulputate, nunc non ullamcorper mattis, nunc orci imperdiet nulla, at laoreet ipsum nisl non leo. Aenean dapibus aliquet sem, ut lacinia magna mattis in.</p><h3>Mauris et euismod enim, eget elementum orci</h3><p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur tempor arcu eu sapien ullamcorper sodales. Aenean eu massa in ante commodo scelerisque vitae sed sapien. Aenean eu dictum arcu. Mauris ultricies dolor eu molestie egestas.<br>Proin feugiat, nunc at pellentesque fringilla, ex purus efficitur dolor, ac pretium odio lacus id leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse eu ipsum viverra dolor tempus vehicula eu eu risus. Praesent rutrum dapibus odio, nec accumsan justo fermentum in. Ut quis neque a ante facilisis bibendum.</p>', '', '', 7, '1', 1, 1, 3, INSTALL_DATE, INSTALL_DATE, NULL, NULL, 1, NULL, '', ''),
(3, 2, 'Nullam molestie, nunc eu consequat', '', 'nullam-molestie-nunc-eu-consequat', 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. ', '<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque. Donec et faucibus justo. Nulla vitae nisl lacus. Fusce tincidunt quam nec vestibulum vestibulum. Vivamus vulputate, nunc non ullamcorper mattis, nunc orci imperdiet nulla, at laoreet ipsum nisl non leo. Aenean dapibus aliquet sem, ut lacinia magna mattis in.</p><h3>Mauris et euismod enim, eget elementum orci</h3><p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur tempor arcu eu sapien ullamcorper sodales. Aenean eu massa in ante commodo scelerisque vitae sed sapien. Aenean eu dictum arcu. Mauris ultricies dolor eu molestie egestas.<br>Proin feugiat, nunc at pellentesque fringilla, ex purus efficitur dolor, ac pretium odio lacus id leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse eu ipsum viverra dolor tempus vehicula eu eu risus. Praesent rutrum dapibus odio, nec accumsan justo fermentum in. Ut quis neque a ante facilisis bibendum.</p>', '', '', 7, '1', 1, 1, 3, INSTALL_DATE, INSTALL_DATE, NULL, NULL, 1, NULL, '', ''),
(4, 1, 'Mon premier article', '', 'mon-premier-article', 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar.', '<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque. Donec et faucibus justo. Nulla vitae nisl lacus. Fusce tincidunt quam nec vestibulum vestibulum. Vivamus vulputate, nunc non ullamcorper mattis, nunc orci imperdiet nulla, at laoreet ipsum nisl non leo. Aenean dapibus aliquet sem, ut lacinia magna mattis in.</p><h3>Mauris et euismod enim, eget elementum orci</h3><p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur tempor arcu eu sapien ullamcorper sodales. Aenean eu massa in ante commodo scelerisque vitae sed sapien. Aenean eu dictum arcu. Mauris ultricies dolor eu molestie egestas.<br>Proin feugiat, nunc at pellentesque fringilla, ex purus efficitur dolor, ac pretium odio lacus id leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse eu ipsum viverra dolor tempus vehicula eu eu risus. Praesent rutrum dapibus odio, nec accumsan justo fermentum in. Ut quis neque a ante facilisis bibendum.</p>', '', '', 7, '1', 1, 1, 4, INSTALL_DATE, INSTALL_DATE, NULL, NULL, 1, NULL, '', ''),
(4, 2, 'Donec gravida eget velit eget pulvinar', '', 'donec-gravida-eget-velit-eget-pulvinar', 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar.', '<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque. Donec et faucibus justo. Nulla vitae nisl lacus. Fusce tincidunt quam nec vestibulum vestibulum. Vivamus vulputate, nunc non ullamcorper mattis, nunc orci imperdiet nulla, at laoreet ipsum nisl non leo. Aenean dapibus aliquet sem, ut lacinia magna mattis in.</p><h3>Mauris et euismod enim, eget elementum orci</h3><p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur tempor arcu eu sapien ullamcorper sodales. Aenean eu massa in ante commodo scelerisque vitae sed sapien. Aenean eu dictum arcu. Mauris ultricies dolor eu molestie egestas.<br>Proin feugiat, nunc at pellentesque fringilla, ex purus efficitur dolor, ac pretium odio lacus id leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse eu ipsum viverra dolor tempus vehicula eu eu risus. Praesent rutrum dapibus odio, nec accumsan justo fermentum in. Ut quis neque a ante facilisis bibendum.</p>', '', '', 7, '1', 1, 1, 4, INSTALL_DATE, INSTALL_DATE, NULL, NULL, 1, NULL, '', '');

-- ============= CREATION OF THE TABLE solutionsCMS_article_file ===========

CREATE TABLE IF NOT EXISTS solutionsCMS_article_file (
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `id_item` int NOT NULL,
    `home` int DEFAULT 0,
    `checked` int DEFAULT 1,
    `rank` int DEFAULT 0,
    `file` varchar(250),
    `label` varchar(250),
    `type` varchar(20),
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_article_file ADD CONSTRAINT article_file_fkey FOREIGN KEY (id_item, lang) REFERENCES solutionsCMS_article(id, lang) ON UPDATE NO ACTION ON DELETE CASCADE;
ALTER TABLE solutionsCMS_article_file ADD CONSTRAINT article_file_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content OF THE TABLE solutionsCMS_article_file
--

INSERT INTO `solutionsCMS_article_file` (`id`, `lang`, `id_item`, `home`, `checked`, `rank`, `file`, `label`, `type`) VALUES
(1, 1, 1, 0, 1, 1, 'sample.jpg', '', 'image'),
(1, 2, 1, 0, 1, 1, 'sample.jpg', '', 'image'),
(2, 1, 2, 0, 1, 2, 'sample2.jpg', '', 'image'),
(2, 2, 2, 0, 1, 2, 'sample2.jpg', '', 'image'),
(3, 1, 3, 0, 1, 3, 'sample3.jpg', '', 'image'),
(3, 2, 3, 0, 1, 3, 'sample3.jpg', '', 'image'),
(4, 1, 4, 0, 1, 4, 'sample4.jpg', '', 'image'),
(4, 2, 4, 0, 1, 4, 'sample4.jpg', '', 'image');

-- ================ CREATION OF THE TABLE solutionsCMS_comment =============

CREATE TABLE IF NOT EXISTS solutionsCMS_comment (
    `id` int NOT NULL AUTO_INCREMENT,
    `item_type` varchar(30),
    `id_item` int,
    `rating` int,
    `checked` int DEFAULT 0,
    `add_date` int,
    `edit_date` int,
    `name` varchar(100),
    `email` varchar(100),
    `title` varchar(250),
    `msg` longtext,
    `ip` varchar(50),
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- ================= CREATION OF THE TABLE solutionsCMS_tag ================

CREATE TABLE IF NOT EXISTS solutionsCMS_tag(
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `value` varchar(250),
    `pages` varchar(250),
    `checked` int DEFAULT 0,
    `rank` int DEFAULT 0,
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_tag ADD CONSTRAINT tag_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

-- ================= CREATION OF THE TABLE solutionsCMS_slide ==============

CREATE TABLE IF NOT EXISTS solutionsCMS_slide(
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `legend` text,
    `url` varchar(250),
    `id_page` int,
    `checked` int DEFAULT 0,
    `rank` int DEFAULT 0,
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_slide ADD CONSTRAINT slide_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE solutionsCMS_slide ADD CONSTRAINT slide_page_fkey FOREIGN KEY (id_page, lang) REFERENCES solutionsCMS_page(id, lang) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content OF THE TABLE solutionsCMS_slide
--

INSERT INTO `solutionsCMS_slide` (`id`, `lang`, `legend`, `url`, `id_page`, `checked`, `rank`) VALUES
(1, 1, '<h1>Best CMS Ever<br><small>Your Website, Your Way!</small></h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.<br>Fusce at fringilla est. In eget porta odio.<br>Sed eget ligula vitae ante iaculis tempus eget a enim. </p><p><a class=\"btn btn-primary\" href=\"#\">Explore</a></p>', '', 1, 1, 1),
(1, 2, '<h1>Best CMS Ever<br><small>Your Website, Your Way!</small></h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.<br>Fusce at fringilla est. In eget porta odio.<br>Sed eget ligula vitae ante iaculis tempus eget a enim. </p><p><a class=\"btn btn-primary\" href=\"#\">Explore</a></p>', '', 1, 1, 1),
(2, 1, '<h1>Best CMS Ever<br><small>Your Website, Your Way!</small></h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.<br>Fusce at fringilla est. In eget porta odio.<br>Sed eget ligula vitae ante iaculis tempus eget a enim. </p><p><a class=\"btn btn-primary\" href=\"#\">Learn more</a></p>', '', 1, 1, 2),
(2, 2, '<h1>Best CMS Ever<br><small>Your Website, Your Way!</small></h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.<br>Fusce at fringilla est. In eget porta odio.<br>Sed eget ligula vitae ante iaculis tempus eget a enim. </p><p><a class=\"btn btn-primary\" href=\"#\">Learn more</a></p>', '', 1, 1, 2);

-- ============== CREATION OF THE TABLE solutionsCMS_slide_file ============

CREATE TABLE IF NOT EXISTS solutionsCMS_slide_file (
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `id_item` int NOT NULL,
    `home` int DEFAULT 0,
    `checked` int DEFAULT 1,
    `rank` int DEFAULT 0,
    `file` varchar(250),
    `label` varchar(250),
    `type` varchar(20),
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_slide_file ADD CONSTRAINT slide_file_fkey FOREIGN KEY (id_item, lang) REFERENCES solutionsCMS_slide(id, lang) ON UPDATE NO ACTION ON DELETE CASCADE;
ALTER TABLE solutionsCMS_slide_file ADD CONSTRAINT slide_file_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content OF THE TABLE solutionsCMS_slide_file
--

INSERT INTO `solutionsCMS_slide_file` (`id`, `lang`, `id_item`, `home`, `checked`, `rank`, `file`, `label`, `type`) VALUES
(1, 1, 1, 0, 1, 2, 'slide1.jpg', '', 'image'),
(1, 2, 1, 0, 1, 2, 'slide1.jpg', '', 'image'),
(2, 1, 2, 0, 1, 3, 'slide2.jpg', '', 'image'),
(2, 2, 2, 0, 1, 3, 'slide2.jpg', '', 'image');

-- =============== CREATION OF THE TABLE solutionsCMS_location =============

CREATE TABLE IF NOT EXISTS solutionsCMS_location(
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(100),
    `address` varchar(250),
    `lat` double,
    `lng` double,
    `checked` int DEFAULT 0,
    `pages` text,
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

--
-- Content OF THE TABLE solutionsCMS_location
--

INSERT INTO `solutionsCMS_location` (`id`, `name`, `address`, `lat`, `lng`, `checked`, `pages`) VALUES
(1, 'Big Ben', 'London SW1A 0AA', '51.500729', '-0.124625', 1, '2');

-- ================ CREATION OF THE TABLE solutionsCMS_message =============

CREATE TABLE IF NOT EXISTS solutionsCMS_message (
    `id` int NOT NULL AUTO_INCREMENT,
    `add_date` int,
    `edit_date` int,
    `name` varchar(100),
    `email` varchar(100),
    `address` longtext,
    `phone` varchar(100),
    `subject` varchar(250),
    `msg` longtext,
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- =============== CREATION OF THE TABLE solutionsCMS_currency =============

CREATE TABLE IF NOT EXISTS solutionsCMS_currency(
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(5),
    `sign` varchar(5),
    `main` int DEFAULT 0,
    `rank` int DEFAULT 0,
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

--
-- Content of the table solutionsCMS_currency
--

INSERT INTO `solutionsCMS_currency` (`id`, `code`, `sign`, `main`, `rank`) VALUES
(1, 'USD', '$', 1, 1),
(2, 'EUR', '€', 0, 2),
(3, 'GBP', '£', 0, 3),
(4, 'INR', '₹', 0, 4),
(5, 'AUD', 'A$', 0, 5),
(6, 'CAD', 'C$', 0, 6),
(7, 'CNY', '¥', 0, 7),
(8, 'TRY', '₺', 0, 8);

-- =============== CREATION OF THE TABLE solutionsCMS_country ==============

CREATE TABLE IF NOT EXISTS solutionsCMS_country(
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(100),
    `code` varchar(3),
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

--
-- Content of the table solutionsCMS_country
--

INSERT INTO solutionsCMS_country VALUES
(null, 'Afghanistan', 'AF'),
(null, 'Åland', 'AX'),
(null, 'Albania', 'AL'),
(null, 'Algeria', 'DZ'),
(null, 'American Samoa', 'AS'),
(null, 'Andorra', 'AD'),
(null, 'Angola', 'AO'),
(null, 'Anguilla', 'AI'),
(null, 'Antarctica', 'AQ'),
(null, 'Antigua and Barbuda', 'AG'),
(null, 'Argentina', 'AR'),
(null, 'Armenia', 'AM'),
(null, 'Aruba', 'AW'),
(null, 'Australia', 'AU'),
(null, 'Austria', 'AT'),
(null, 'Azerbaijan', 'AZ'),
(null, 'Bahamas', 'BS'),
(null, 'Bahrain', 'BH'),
(null, 'Bangladesh', 'BD'),
(null, 'Barbados', 'BB'),
(null, 'Belarus', 'BY'),
(null, 'Belgium', 'BE'),
(null, 'Belize', 'BZ'),
(null, 'Benin', 'BJ'),
(null, 'Bermuda', 'BM'),
(null, 'Bhutan', 'BT'),
(null, 'Bolivia', 'BO'),
(null, 'Bonaire', 'BQ'),
(null, 'Bosnia and Herzegovina', 'BA'),
(null, 'Botswana', 'BW'),
(null, 'Bouvet Island', 'BV'),
(null, 'Brazil', 'BR'),
(null, 'British Indian Ocean Territory', 'IO'),
(null, 'British Virgin Islands', 'VG'),
(null, 'Brunei', 'BN'),
(null, 'Bulgaria', 'BG'),
(null, 'Burkina Faso', 'BF'),
(null, 'Burundi', 'BI'),
(null, 'Cambodia', 'KH'),
(null, 'Cameroon', 'CM'),
(null, 'Canada', 'CA'),
(null, 'Cape Verde', 'CV'),
(null, 'Cayman Islands', 'KY'),
(null, 'Central African Republic', 'CF'),
(null, 'Chad', 'TD'),
(null, 'Chile', 'CL'),
(null, 'China', 'CN'),
(null, 'Christmas Island', 'CX'),
(null, 'Cocos [Keeling] Islands', 'CC'),
(null, 'Colombia', 'CO'),
(null, 'Comoros', 'KM'),
(null, 'Cook Islands', 'CK'),
(null, 'Costa Rica', 'CR'),
(null, 'Croatia', 'HR'),
(null, 'Cuba', 'CU'),
(null, 'Curacao', 'CW'),
(null, 'Cyprus', 'CY'),
(null, 'Czech Republic', 'CZ'),
(null, 'Democratic Republic of the Congo', 'CD'),
(null, 'Denmark', 'DK'),
(null, 'Djibouti', 'DJ'),
(null, 'Dominica', 'DM'),
(null, 'Dominican Republic', 'DO'),
(null, 'East Timor', 'TL'),
(null, 'Ecuador', 'EC'),
(null, 'Egypt', 'EG'),
(null, 'El Salvador', 'SV'),
(null, 'Equatorial Guinea', 'GQ'),
(null, 'Eritrea', 'ER'),
(null, 'Estonia', 'EE'),
(null, 'Ethiopia', 'ET'),
(null, 'Falkland Islands', 'FK'),
(null, 'Faroe Islands', 'FO'),
(null, 'Fiji', 'FJ'),
(null, 'Finland', 'FI'),
(null, 'France', 'FR'),
(null, 'French Guiana', 'GF'),
(null, 'French Polynesia', 'PF'),
(null, 'French Southern Territories', 'TF'),
(null, 'Gabon', 'GA'),
(null, 'Gambia', 'GM'),
(null, 'Georgia', 'GE'),
(null, 'Germany', 'DE'),
(null, 'Ghana', 'GH'),
(null, 'Gibraltar', 'GI'),
(null, 'Greece', 'GR'),
(null, 'Greenland', 'GL'),
(null, 'Grenada', 'GD'),
(null, 'Guadeloupe', 'GP'),
(null, 'Guam', 'GU'),
(null, 'Guatemala', 'GT'),
(null, 'Guernsey', 'GG'),
(null, 'Guinea', 'GN'),
(null, 'Guinea-Bissau', 'GW'),
(null, 'Guyana', 'GY'),
(null, 'Haiti', 'HT'),
(null, 'Heard Island and McDonald Islands', 'HM'),
(null, 'Honduras', 'HN'),
(null, 'Hong Kong', 'HK'),
(null, 'Hungary', 'HU'),
(null, 'Iceland', 'IS'),
(null, 'India', 'IN'),
(null, 'Indonesia', 'ID'),
(null, 'Iran', 'IR'),
(null, 'Iraq', 'IQ'),
(null, 'Ireland', 'IE'),
(null, 'Isle of Man', 'IM'),
(null, 'Israel', 'IL'),
(null, 'Italy', 'IT'),
(null, 'Ivory Coast', 'CI'),
(null, 'Jamaica', 'JM'),
(null, 'Japan', 'JP'),
(null, 'Jersey', 'JE'),
(null, 'Jordan', 'JO'),
(null, 'Kazakhstan', 'KZ'),
(null, 'Kenya', 'KE'),
(null, 'Kiribati', 'KI'),
(null, 'Kosovo', 'XK'),
(null, 'Kuwait', 'KW'),
(null, 'Kyrgyzstan', 'KG'),
(null, 'Laos', 'LA'),
(null, 'Latvia', 'LV'),
(null, 'Lebanon', 'LB'),
(null, 'Lesotho', 'LS'),
(null, 'Liberia', 'LR'),
(null, 'Libya', 'LY'),
(null, 'Liechtenstein', 'LI'),
(null, 'Lithuania', 'LT'),
(null, 'Luxembourg', 'LU'),
(null, 'Macao', 'MO'),
(null, 'Macedonia', 'MK'),
(null, 'Madagascar', 'MG'),
(null, 'Malawi', 'MW'),
(null, 'Malaysia', 'MY'),
(null, 'Maldives', 'MV'),
(null, 'Mali', 'ML'),
(null, 'Malta', 'MT'),
(null, 'Marshall Islands', 'MH'),
(null, 'Martinique', 'MQ'),
(null, 'Mauritania', 'MR'),
(null, 'Mauritius', 'MU'),
(null, 'Mayotte', 'YT'),
(null, 'Mexico', 'MX'),
(null, 'Micronesia', 'FM'),
(null, 'Moldova', 'MD'),
(null, 'Monaco', 'MC'),
(null, 'Mongolia', 'MN'),
(null, 'Montenegro', 'ME'),
(null, 'Montserrat', 'MS'),
(null, 'Morocco', 'MA'),
(null, 'Mozambique', 'MZ'),
(null, 'Myanmar [Burma]', 'MM'),
(null, 'Namibia', 'NA'),
(null, 'Nauru', 'NR'),
(null, 'Nepal', 'NP'),
(null, 'Netherlands', 'NL'),
(null, 'New Caledonia', 'NC'),
(null, 'New Zealand', 'NZ'),
(null, 'Nicaragua', 'NI'),
(null, 'Niger', 'NE'),
(null, 'Nigeria', 'NG'),
(null, 'Niue', 'NU'),
(null, 'Norfolk Island', 'NF'),
(null, 'North Korea', 'KP'),
(null, 'Northern Mariana Islands', 'MP'),
(null, 'Norway', 'NO'),
(null, 'Oman', 'OM'),
(null, 'Pakistan', 'PK'),
(null, 'Palau', 'PW'),
(null, 'Palestine', 'PS'),
(null, 'Panama', 'PA'),
(null, 'Papua New Guinea', 'PG'),
(null, 'Paraguay', 'PY'),
(null, 'Peru', 'PE'),
(null, 'Philippines', 'PH'),
(null, 'Pitcairn Islands', 'PN'),
(null, 'Poland', 'PL'),
(null, 'Portugal', 'PT'),
(null, 'Puerto Rico', 'PR'),
(null, 'Qatar', 'QA'),
(null, 'Republic of the Congo', 'CG'),
(null, 'Réunion', 'RE'),
(null, 'Romania', 'RO'),
(null, 'Russia', 'RU'),
(null, 'Rwanda', 'RW'),
(null, 'Saint Barthélemy', 'BL'),
(null, 'Saint Helena', 'SH'),
(null, 'Saint Kitts and Nevis', 'KN'),
(null, 'Saint Lucia', 'LC'),
(null, 'Saint Martin', 'MF'),
(null, 'Saint Pierre and Miquelon', 'PM'),
(null, 'Saint Vincent and the Grenadines', 'VC'),
(null, 'Samoa', 'WS'),
(null, 'San Marino', 'SM'),
(null, 'São Tomé and Príncipe', 'ST'),
(null, 'Saudi Arabia', 'SA'),
(null, 'Senegal', 'SN'),
(null, 'Serbia', 'RS'),
(null, 'Seychelles', 'SC'),
(null, 'Sierra Leone', 'SL'),
(null, 'Singapore', 'SG'),
(null, 'Sint Maarten', 'SX'),
(null, 'Slovakia', 'SK'),
(null, 'Slovenia', 'SI'),
(null, 'Solomon Islands', 'SB'),
(null, 'Somalia', 'SO'),
(null, 'South Africa', 'ZA'),
(null, 'South Georgia and the South Sandwich Islands', 'GS'),
(null, 'South Korea', 'KR'),
(null, 'South Sudan', 'SS'),
(null, 'Spain', 'ES'),
(null, 'Sri Lanka', 'LK'),
(null, 'Sudan', 'SD'),
(null, 'Suriname', 'SR'),
(null, 'Svalbard and Jan Mayen', 'SJ'),
(null, 'Swaziland', 'SZ'),
(null, 'Sweden', 'SE'),
(null, 'Switzerland', 'CH'),
(null, 'Syria', 'SY'),
(null, 'Taiwan', 'TW'),
(null, 'Tajikistan', 'TJ'),
(null, 'Tanzania', 'TZ'),
(null, 'Thailand', 'TH'),
(null, 'Togo', 'TG'),
(null, 'Tokelau', 'TK'),
(null, 'Tonga', 'TO'),
(null, 'Trinidad and Tobago', 'TT'),
(null, 'Tunisia', 'TN'),
(null, 'Turkey', 'TR'),
(null, 'Turkmenistan', 'TM'),
(null, 'Turks and Caicos Islands', 'TC'),
(null, 'Tuvalu', 'TV'),
(null, 'U.S. Minor Outlying Islands', 'UM'),
(null, 'U.S. Virgin Islands', 'VI'),
(null, 'Uganda', 'UG'),
(null, 'Ukraine', 'UA'),
(null, 'United Arab Emirates', 'AE'),
(null, 'United Kingdom', 'GB'),
(null, 'United States', 'US'),
(null, 'Uruguay', 'UY'),
(null, 'Uzbekistan', 'UZ'),
(null, 'Vanuatu', 'VU'),
(null, 'Vatican City', 'VA'),
(null, 'Venezuela', 'VE'),
(null, 'Vietnam', 'VN'),
(null, 'Wallis and Futuna', 'WF'),
(null, 'Western Sahara', 'EH'),
(null, 'Yemen', 'YE'),
(null, 'Zambia', 'ZM'),
(null, 'Zimbabwe', 'ZW');

-- =============== CREATION OF THE TABLE solutionsCMS_social ===============

CREATE TABLE IF NOT EXISTS solutionsCMS_social(
    `id` int NOT NULL AUTO_INCREMENT,
    `type` varchar(50),
    `url` text,
    `checked` int DEFAULT 1,
    `rank` int DEFAULT 0,
    PRIMARY KEY(id)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- ============ CREATION OF THE TABLE solutionsCMS_email_content ===========

CREATE TABLE IF NOT EXISTS solutionsCMS_email_content(
    `id` int NOT NULL AUTO_INCREMENT,
    `lang` int NOT NULL,
    `name` varchar(50),
    `subject` varchar(250),
    `content` text,
    PRIMARY KEY(id, lang)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE solutionsCMS_email_content ADD CONSTRAINT email_content_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Content of the table solutionsCMS_email_content
--

INSERT INTO `solutionsCMS_email_content` (`id`, `lang`, `name`, `subject`, `content`) VALUES
(1, 1, 'CONTACT', 'Contact', '<b>Nom :</b> {name}<br><b>Adresse :</b> {address}<br><b>Téléphone :</b> {phone}<br><b>E-mail :</b> {email}<br><b>Message :</b><br>{msg}'),
(1, 2, 'CONTACT', 'Contact', '<b>Name:</b> {name}<br><b>Address:</b> {address}<br><b>Phone:</b> {phone}<br><b>E-mail:</b> {email}<br><b>Message:</b><br>{msg}');

-- ============== CREATION OF THE TABLE solutionsCMS_popup ==============

	CREATE TABLE solutionsCMS_popup(
		`id` int NOT NULL AUTO_INCREMENT,
		`lang` int NOT NULL,
		`title` varchar(250),
		`content` text,
		`allpages` text,
		`pages` text,
		`background` varchar(20),
		`checked` int DEFAULT 0,
		`publish_date` int,
		`unpublish_date` int,
		PRIMARY KEY(id, lang)
	) ENGINE=INNODB DEFAULT CHARSET=utf8;

	ALTER TABLE solutionsCMS_popup ADD CONSTRAINT popup_lang_fkey FOREIGN KEY (lang) REFERENCES solutionsCMS_lang(id) ON DELETE CASCADE ON UPDATE NO ACTION;
