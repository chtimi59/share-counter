<?PHP

/* config */
define('TITLE', 'Share-Counter');
define('LOG_NO_REPLY_EMAIL', $GLOBALS['CONFIG']['noreply_email']);
define('ADMIN_UUID', $GLOBALS['CONFIG']['admin']);
define('SESSION_TIMEOUT_MINUTES',60);

/* tables */
define('LOG_USER_TABLE',     'scounter_users');
define('LOG_TMP_USER_TABLE', 'scounter_tmp_users');
define('COUNTERLIST_TABLE',  'scounter_counterlist');
define('GALLERY_TABLE',      'scounter_gallery');


/* class */
define('ACTION_CLASSRANGE',   50);

define('ACTION_CLASS_ROOT',             000);
define('ACTION_NONE',                   ACTION_CLASS_ROOT+0);

define('ACTION_CLASS_LOG',              100);
define('ACTION_LOGIN',                  ACTION_CLASS_LOG+0);
define('ACTION_LOGIN_SETTING',          ACTION_CLASS_LOG+1);

define('ACTION_CLASS_COUNTER',          200);
define('ACTION_COUNTER_LIST',           ACTION_CLASS_COUNTER+0);
define('ACTION_ACCOUNT_SETTING',        ACTION_CLASS_COUNTER+1);

define('ACTION_CLASS_RECORD',           300);
define('ACTION_RECORD_LIST',            ACTION_CLASS_RECORD+0);

define('ACTION_CLASS_STATICPAGE',       400);
define('ACTION_STATIC_SDK',             ACTION_CLASS_STATICPAGE+0);
define('ACTION_STATIC_TERMS',           ACTION_CLASS_STATICPAGE+1);
define('ACTION_STATIC_CONTACT',         ACTION_CLASS_STATICPAGE+2);

?>