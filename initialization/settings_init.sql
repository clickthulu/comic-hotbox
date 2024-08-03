INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values
    ('allow_user_signup', '0', '0', NOW(), 'bool', 'Allow users to register without approval.', 'Open user registration', null),
    ('server_name', null, null, NOW(), 'string', 'Name of this Server', 'Server Name', null),
    ('server_url', null, null, NOW(), 'string', 'Server\'s URL', 'Server URL', null),
    ('user_storage_path', 'storage', 'storage', NOW(), 'string', 'Path for user storage', 'User Storage Path', null),
    ('email_from_name', null, null, NOW(), 'string', 'Name used to send mail', 'From Name', null),
    ('email_from_address', null, null, NOW(), 'string', 'Email address used to send mail', 'From Address', null),
    ('image_limit', 6, 6, NOW(), "int", "Maximum number of images per comic", "Image Limit", null),
    ('debug_mode', '0', '0', NOW(), 'bool', 'Turn on debug features for testing', 'Debug Mode:', null)
;