#!/bin/bash
# Patch apllying tool template
# v0.1.2
# (c) Copyright 2013. Magento Inc.
#
# DO NOT CHANGE ANY LINE IN THIS FILE.

# 1. Check required system tools
_check_installed_tools() {
    local missed=""

    until [ -z "$1" ]; do
        type -t $1 >/dev/null 2>/dev/null
        if (( $? != 0 )); then
            missed="$missed $1"
        fi
        shift
    done

    echo $missed
}

REQUIRED_UTILS='sed patch'
MISSED_REQUIRED_TOOLS=`_check_installed_tools $REQUIRED_UTILS`
if (( `echo $MISSED_REQUIRED_TOOLS | wc -w` > 0 ));
then
    echo -e "Error! Some required system tools, that are utilized in this sh script, are not installed:\nTool(s) \"$MISSED_REQUIRED_TOOLS\" is(are) missed, please install it(them)."
    exit 1
fi

# 2. Determine bin path for system tools
CAT_BIN=`which cat`
PATCH_BIN=`which patch`
SED_BIN=`which sed`
PWD_BIN=`which pwd`
BASENAME_BIN=`which basename`

BASE_NAME=`$BASENAME_BIN "$0"`

# 3. Help menu
if [ "$1" = "-?" -o "$1" = "-h" -o "$1" = "--help" ]
then
    $CAT_BIN << EOFH
Usage: sh $BASE_NAME [--help] [-R|--revert] [--list]
Apply embedded patch.

-R, --revert    Revert previously applied embedded patch
--list          Show list of applied patches
--help          Show this help message
EOFH
    exit 0
fi

# 4. Get "revert" flag and "list applied patches" flag
REVERT_FLAG=
SHOW_APPLIED_LIST=0
if [ "$1" = "-R" -o "$1" = "--revert" ]
then
    REVERT_FLAG=-R
fi
if [ "$1" = "--list" ]
then
    SHOW_APPLIED_LIST=1
fi

# 5. File pathes
CURRENT_DIR=`$PWD_BIN`/
APP_ETC_DIR=`echo "$CURRENT_DIR""app/etc/"`
APPLIED_PATCHES_LIST_FILE=`echo "$APP_ETC_DIR""applied.patches.list"`

# 6. Show applied patches list if requested
if [ "$SHOW_APPLIED_LIST" -eq 1 ] ; then
    echo -e "Applied/reverted patches list:"
    if [ -e "$APPLIED_PATCHES_LIST_FILE" ]
    then
        if [ ! -r "$APPLIED_PATCHES_LIST_FILE" ]
        then
            echo "ERROR: \"$APPLIED_PATCHES_LIST_FILE\" must be readable so applied patches list can be shown."
            exit 1
        else
            $SED_BIN -n "/SUP-\|SUPEE-/p" $APPLIED_PATCHES_LIST_FILE
        fi
    else
        echo "<empty>"
    fi
    exit 0
fi

# 7. Check applied patches track file and its directory
_check_files() {
    if [ ! -e "$APP_ETC_DIR" ]
    then
        echo "ERROR: \"$APP_ETC_DIR\" must exist for proper tool work."
        exit 1
    fi

    if [ ! -w "$APP_ETC_DIR" ]
    then
        echo "ERROR: \"$APP_ETC_DIR\" must be writeable for proper tool work."
        exit 1
    fi

    if [ -e "$APPLIED_PATCHES_LIST_FILE" ]
    then
        if [ ! -w "$APPLIED_PATCHES_LIST_FILE" ]
        then
            echo "ERROR: \"$APPLIED_PATCHES_LIST_FILE\" must be writeable for proper tool work."
            exit 1
        fi
    fi
}

_check_files

# 8. Apply/revert patch
# Note: there is no need to check files permissions for files to be patched.
# "patch" tool will not modify any file if there is not enough permissions for all files to be modified.
# Get start points for additional information and patch data
SKIP_LINES=$((`$SED_BIN -n "/^__PATCHFILE_FOLLOWS__$/=" "$CURRENT_DIR""$BASE_NAME"` + 1))
ADDITIONAL_INFO_LINE=$(($SKIP_LINES - 3))p

_apply_revert_patch() {
    DRY_RUN_FLAG=
    if [ "$1" = "dry-run" ]
    then
        DRY_RUN_FLAG=" --dry-run"
        echo "Checking if patch can be applied/reverted successfully..."
    fi
    PATCH_APPLY_REVERT_RESULT=`$SED_BIN -e '1,/^__PATCHFILE_FOLLOWS__$/d' "$CURRENT_DIR""$BASE_NAME" | $PATCH_BIN $DRY_RUN_FLAG $REVERT_FLAG -p0`
    PATCH_APPLY_REVERT_STATUS=$?
    if [ $PATCH_APPLY_REVERT_STATUS -eq 1 ] ; then
        echo -e "ERROR: Patch can't be applied/reverted successfully.\n\n$PATCH_APPLY_REVERT_RESULT"
        exit 1
    fi
    if [ $PATCH_APPLY_REVERT_STATUS -eq 2 ] ; then
        echo -e "ERROR: Patch can't be applied/reverted successfully."
        exit 2
    fi
}

REVERTED_PATCH_MARK=
if [ -n "$REVERT_FLAG" ]
then
    REVERTED_PATCH_MARK=" | REVERTED"
fi

_apply_revert_patch dry-run
_apply_revert_patch

# 9. Track patch applying result
echo "Patch was applied/reverted successfully."
ADDITIONAL_INFO=`$SED_BIN -n ""$ADDITIONAL_INFO_LINE"" "$CURRENT_DIR""$BASE_NAME"`
APPLIED_REVERTED_ON_DATE=`date -u +"%F %T UTC"`
APPLIED_REVERTED_PATCH_INFO=`echo -n "$APPLIED_REVERTED_ON_DATE"" | ""$ADDITIONAL_INFO""$REVERTED_PATCH_MARK"`
echo -e "$APPLIED_REVERTED_PATCH_INFO\n$PATCH_APPLY_REVERT_RESULT\n\n" >> "$APPLIED_PATCHES_LIST_FILE"

exit 0


SUPEE-5344 | EE_1.14.1.0 | v1 | a5c9abcb6a387aabd6b33ebcb79f6b7a97bbde77 | Thu Feb 5 19:14:49 2015 +0200 | v1.14.1.0..HEAD

__PATCHFILE_FOLLOWS__
diff --git app/code/core/Mage/Admin/Model/Observer.php app/code/core/Mage/Admin/Model/Observer.php
index bd00181..6a5281c 100644
--- app/code/core/Mage/Admin/Model/Observer.php
+++ app/code/core/Mage/Admin/Model/Observer.php
@@ -44,6 +44,10 @@ class Mage_Admin_Model_Observer
     {
         $session = Mage::getSingleton('admin/session');
         /** @var $session Mage_Admin_Model_Session */
+
+        /**
+         * @var $request Mage_Core_Controller_Request_Http
+         */
         $request = Mage::app()->getRequest();
         $user = $session->getUser();
 
@@ -58,7 +62,7 @@ class Mage_Admin_Model_Observer
         if (in_array($requestedActionName, $openActions)) {
             $request->setDispatched(true);
         } else {
-            if($user) {
+            if ($user) {
                 $user->reload();
             }
             if (!$user || !$user->getId()) {
@@ -69,13 +73,14 @@ class Mage_Admin_Model_Observer
                     $session->login($username, $password, $request);
                     $request->setPost('login', null);
                 }
-                if (!$request->getParam('forwarded')) {
+                if (!$request->getInternallyForwarded()) {
+                    $request->setInternallyForwarded();
                     if ($request->getParam('isIframe')) {
                         $request->setParam('forwarded', true)
                             ->setControllerName('index')
                             ->setActionName('deniedIframe')
                             ->setDispatched(false);
-                    } elseif($request->getParam('isAjax')) {
+                    } elseif ($request->getParam('isAjax')) {
                         $request->setParam('forwarded', true)
                             ->setControllerName('index')
                             ->setActionName('deniedJson')
diff --git app/code/core/Mage/Core/Controller/Request/Http.php app/code/core/Mage/Core/Controller/Request/Http.php
index 6513db9..31eb6d6 100644
--- app/code/core/Mage/Core/Controller/Request/Http.php
+++ app/code/core/Mage/Core/Controller/Request/Http.php
@@ -76,6 +76,13 @@ class Mage_Core_Controller_Request_Http extends Zend_Controller_Request_Http
     protected $_beforeForwardInfo = array();
 
     /**
+     * Flag for recognizing if request internally forwarded
+     *
+     * @var bool
+     */
+    protected $_internallyForwarded = false;
+
+    /**
      * Returns ORIGINAL_PATH_INFO.
      * This value is calculated instead of reading PATH_INFO
      * directly from $_SERVER due to cross-platform differences.
@@ -534,4 +541,26 @@ class Mage_Core_Controller_Request_Http extends Zend_Controller_Request_Http
         }
         return false;
     }
+
+    /**
+     * Define that request was forwarded internally
+     *
+     * @param boolean $flag
+     * @return Mage_Core_Controller_Request_Http
+     */
+    public function setInternallyForwarded($flag = true)
+    {
+        $this->_internallyForwarded = (bool)$flag;
+        return $this;
+    }
+
+    /**
+     * Checks if request was forwarded internally
+     *
+     * @return bool
+     */
+    public function getInternallyForwarded()
+    {
+        return $this->_internallyForwarded;
+    }
 }
diff --git app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/AuthorizeController.php app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/AuthorizeController.php
index c30d273..36542f9 100644
--- app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/AuthorizeController.php
+++ app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/AuthorizeController.php
@@ -55,7 +55,7 @@ class Mage_Oauth_Adminhtml_Oauth_AuthorizeController extends Mage_Adminhtml_Cont
      */
     public function preDispatch()
     {
-        $this->getRequest()->setParam('forwarded', true);
+        Mage::app()->getRequest()->setInternallyForwarded();
 
         // check login data before it set null in Mage_Admin_Model_Observer::actionPreDispatchAdmin
         $loginError = $this->_checkLoginIsEmpty();
diff --git app/code/core/Mage/XmlConnect/Model/Observer.php app/code/core/Mage/XmlConnect/Model/Observer.php
index e6cb947..36142ac 100644
--- app/code/core/Mage/XmlConnect/Model/Observer.php
+++ app/code/core/Mage/XmlConnect/Model/Observer.php
@@ -143,7 +143,7 @@ class Mage_XmlConnect_Model_Observer
         /** @var $request Mage_Core_Controller_Request_Http */
         $request = Mage::app()->getRequest();
         if (true === $this->_checkAdminController($request, $event->getControllerAction())) {
-            $request->setParam('forwarded', true)->setDispatched(true);
+            $request->setInternallyForwarded()->setDispatched(true);
         }
     }
 
@@ -160,7 +160,7 @@ class Mage_XmlConnect_Model_Observer
         if (false === $this->_checkAdminController($request, $event->getControllerAction())
             && !Mage::getSingleton('admin/session')->isLoggedIn()
         ) {
-            $request->setParam('forwarded', true)->setRouteName('adminhtml')->setControllerName('connect_user')
+            $request->setInternallyForwarded()->setRouteName('adminhtml')->setControllerName('connect_user')
                 ->setActionName('loginform')->setDispatched(false);
         }
     }
diff --git lib/Varien/Db/Adapter/Pdo/Mysql.php lib/Varien/Db/Adapter/Pdo/Mysql.php
index 2226331..d1c6942 100644
--- lib/Varien/Db/Adapter/Pdo/Mysql.php
+++ lib/Varien/Db/Adapter/Pdo/Mysql.php
@@ -2834,10 +2834,6 @@ class Varien_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql implements V
 
         $query = '';
         if (is_array($condition)) {
-            if (isset($condition['field_expr'])) {
-                $fieldName = str_replace('#?', $this->quoteIdentifier($fieldName), $condition['field_expr']);
-                unset($condition['field_expr']);
-            }
             $key = key(array_intersect_key($condition, $conditionKeyMap));
 
             if (isset($condition['from']) || isset($condition['to'])) {
