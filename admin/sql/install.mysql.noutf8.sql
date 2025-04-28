/* Added DEFAULT to asset_id
   Default project text values to NULL
   Changed projectfields description to VARCHAR(1000)
   Brainforge.uk 2025/04/28
 */
DROP TABLE IF EXISTS `#__f2c_fieldcontent`;
DROP TABLE IF EXISTS `#__f2c_fieldtype`;
DROP TABLE IF EXISTS `#__f2c_form`;
DROP TABLE IF EXISTS `#__f2c_project`;
DROP TABLE IF EXISTS `#__f2c_projectfields`;
DROP TABLE IF EXISTS `#__f2c_translation`;

CREATE TABLE  `#__f2c_project` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `asset_id` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL default '',
  `created_by` int(10) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `version` varchar(10) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `settings` text NOT NULL,
  `attribs` text NOT NULL,
  `metadata` text NULL,
  `metakey` text NULL,
  `metadesc` text NULL,
  `images` text NULL,
  `urls` text NULL,
  PRIMARY KEY  (`id`)
);	  

CREATE TABLE  `#__f2c_projectfields` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `projectid` int(10) unsigned NOT NULL default '0',
  `fieldname` varchar(45) NOT NULL default '',
  `title` varchar(45) NOT NULL default '',
  `description` varchar(1000) NOT NULL default '',
  `fieldtypeid` int(10) unsigned NOT NULL default '0',
  `settings` text,
  `ordering` int(10) unsigned NOT NULL default '0',
  `frontvisible` tinyint(1) unsigned NOT NULL default '1',    
  PRIMARY KEY  (`id`)
);			  

CREATE TABLE  `#__f2c_fieldcontent` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `formid` int(10) unsigned NOT NULL default '0',
  `fieldid` int(10) unsigned NOT NULL default '0',
  `attribute` varchar(32) NOT NULL DEFAULT 'VALUE',
  `content` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE  `#__f2c_fieldtype` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `description` varchar(45) NOT NULL default '',
  `name` varchar(45) NOT NULL default '', 
  `classification_id` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
) CHARACTER SET `utf8`;

INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (1, 'Single line text (textbox)', 'Singlelinetext', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (2, 'Multi-line text (text area)', 'Multilinetext', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (3, 'Multi-line text (editor)', 'Editor', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (4, 'Check box', 'Checkbox', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (5, 'Single select list', 'Singleselectlist', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (6, 'Image', 'Image', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (7, 'IFrame', 'Iframe', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (8, 'E-mail', 'Email', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (9, 'Hyperlink', 'Hyperlink', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (10, 'Multi select list (checkboxes)', 'Multiselectlist', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (11, 'Info Text', 'Infotext', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (12, 'Date Picker', 'Datepicker', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (13, 'Display List', 'Displaylist', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (14, 'File Upload', 'Fileupload', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (15, 'Database Lookup', 'Databaselookup', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (16, 'Geo Coder', 'Geocoder', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (17, 'Database Lookup (Multi select)', 'Databaselookupmulti', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (18, 'Image Gallery', 'Imagegallery', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (19, 'Color Picker', 'Colorpicker', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (20, 'Joomla Id', 'Joomlaid', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (21, 'Joomla Title', 'Joomlatitle', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (22, 'Joomla Alias', 'Joomlaalias', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (23, 'Joomla Meta Description', 'Joomlametadescription', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (24, 'Joomla Meta Keywords', 'Joomlametakeywords', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (25, 'Joomla Category', 'Joomlacategory', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (26, 'Joomla Author', 'Joomlacreatedby', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (27, 'Joomla Author Alias', 'Joomlacreatedbyalias', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (28, 'Joomla Access', 'Joomlaaccess', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (29, 'Joomla Created Date', 'Joomlacreated', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (30, 'Joomla Start Publishing Date', 'Joomlapublishup', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (31, 'Joomla End Publishing Date', 'Joomlapublishdown', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (32, 'Joomla Featured', 'Joomlafeatured', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (33, 'Joomla Language', 'Joomlalanguage', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (34, 'Joomla State', 'Joomlapublished', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (35, 'Joomla Tags', 'Joomlatags', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (36, 'Joomla Associatons', 'Joomlaassociations', 0);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (37, 'Captcha', 'Captcha', 1);
INSERT INTO `#__f2c_fieldtype` (`id`, `description`, `name`, `classification_id`) VALUES (38, 'Template Selection', 'Joomlatemplate', 0);

CREATE TABLE  `#__f2c_form` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `asset_id` int(10) unsigned NOT NULL DEFAULT 0,
  `projectid` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `created_by` int(10) unsigned NOT NULL default '0',
  `created_by_alias` varchar(255) NOT NULL, 
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0', 
  `metakey` text,
  `metadesc` text,
  `catid` int(10) NOT NULL default '0',
  `intro_template` varchar(100) NOT NULL default '',
  `main_template` varchar(100),
  `reference_id` int,
  `ordering` int(11) NOT NULL default '0',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `featured` tinyint(3) NOT NULL DEFAULT '0',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `attribs` text NOT NULL,
  `metadata` text NOT NULL,
  `language` char(7) NOT NULL,  
  `extended` text NOT NULL,  
  PRIMARY KEY  (`id`)
);	  

CREATE TABLE `#__f2c_translation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `language_id` varchar(10) NOT NULL default '',
  `reference_id` int(10) unsigned NOT NULL default '0',
  `title_translation` mediumtext NOT NULL,
  `description_translation` mediumtext,
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
);