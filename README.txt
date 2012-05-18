How to use this extension
-------------------------

1) What does this extension provides?
	With this extension, you can define this two things (if FE-user doesn't change his password):
	- define a duration, after a FE-user will be added to a special FE-usergroup
	- define a duration, after a FE-user will be deactivated (deleted or hidden)

2) How to configure this extension?
 	2.1) create a FE-usergroup
 	2.2) place a tt_content-element at your homepage, which the FE-user will see, if he logs into the TYPO3-frontend
	     The tt_content-element should contain a text ala "change your password in the next time, or your account will be deleted".
	     The tt_content-element must be assigned with the FE-usergroup, which you created at point 2.1
	2.3) install this extension
	     Inside the extension-manager, you must confige the UID of the FE-usergroup, which you created at point 2.1
	     If you have FE-users, which should be ignored, define their username-prefix also in the extension-manager
	2.4) configure scheduler-task "Detect users with expired passwords (feuser_passwordexpiration)" and define the "Expiration duration"
	     ==> If FE-user hasn't changed his password within the "Expiration duration", than the FE-user will automatically join the FE-usergroup, which you created at point 2.1
	2.5) configure scheduler-task "Deactivate users with expired passwords (feuser_passwordexpiration)" and define the "Expiration duration" and the "Deactivation-Type"
	     ==> If FE-user hasn't changed his password within the "Expiration duration", than the FE-user will automatically be deactivated (deleted or hidden)

3) How to reset expired FE-User?
	3.1) Go to BE-Module 'Functions'
	3.2) Select the Sys-Folder, where your (expired) FE-User are stored
	3.3) Select Option 'Reset Expired FrontendUser'
	3.4) Click on button 'Reset Expired FrontendUser'

4) Dependencies
	This extension only works fine, if you use the feuserregister-extension to update passwords of FE-users (or you write your own Hooks by extending the class Tx_FeuserPasswordexpiration_Hooks_AbstractHook)!