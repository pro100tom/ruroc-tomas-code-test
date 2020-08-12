# ruroc-tomas-code-test

Module installation
-
1. copy the contents to the Magento root
2. run bin/magento setup:upgrade

Module execution
-
1. run bin/magento ruroc:tomas-code-test:update-order-email
2. when prompted enter email address or order id
3. - if the ordes(s) exist(s) the program will tell that and promp to enter the new email address
   - if the order(s) do(es) not exist the program will tell that
	
Brief description of module functionality
-
- An employee should be able to edit the email address of an existing order in Magento.
To keep it simple for this task, this should be able to be done via the CLI instead of in the back office.
The user should be able to provide either an order ID, or an email address.
It will then use the given information to find any matching orders.
If there's a match, the user should then be prompted to provide a new email address.
Any matching orders should then be updated with this new email address.
The code should be written as if it were to be used on a production website.

Authors
-
- Tomas Baranauskas
