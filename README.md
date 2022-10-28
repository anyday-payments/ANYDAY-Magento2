# Anyday
Anyday is a fair and transparent installment payment method you can add to your online store. It is interest-free and without any unexpected expenses and unpleasant surprises.

The extension allows your customers to split their payments into 4 equal installments. The first installment is always paid at checkout and the remaining installments are paid on the last banking day of the following three months.

Anyday is always completely free of interest and your customers pay no additional fees as long as their installments are paid on time. In short: Anyday is both fair and transparent.

To sign up for Anyday customers go through a quick but thorough credit evaluation and upon being accepted they will be granted credit. The credit with Anyday is revolving, which allows customers to utilize their available credit multiple times after paying off installments. Regardless of how many times a customer chooses to utilize their credit the terms remain the same - no fees, no interest.

When you sign an agreement and implement Anyday in your online store you will be added to Anyday’s shop collection. Furthermore, Anyday will upon request provide you with marketing material, feature you in their consumer newsletters, and link to your store via social media. All are completely free of charge.

Anyday assumes the credit risk when a customer chooses to pay with Anyday so you can focus on what you do best - running your business. Orders will be paid out to you in full on a weekly basis.

It is in everyone’s best interest that customers pay installments on time. Therefore, Anyday makes sure to inform customers of when they need to pay and how much they need to pay.

Anyday is fully compliant with Danish legislation.
## Configuration
- To enable Anyday payment go to Backend > Configuration > Sales > Payment Method > Anyday > General Configuration, Select "Module Enable" as "Yes"
- Register your account using account credentials or API keys in configuration.
- To enable pricetag, Select Enable as "Yes" in appropriate configuration section. 
## Account & Pricing
An Anyday account is required to use this extension. If you do not have an Anyday account the extension will not create an account during the installation. Need an account? Sign up for one **[here](https://www.anyday.io/webshop)**.

Please check the Anyday pricing as fees will apply when using Anyday to process your transactions.

## How To Get Started
After signing up for an Anyday account and installing this extension, you will be required to configure the extension to properly handle payments in your system according to your specified requirements along with displaying the Anyday price widget. If you need any help with configuring this extension, feel free to reach out to our onboarding team at onboarding@anyday.io.
å
## Changelog

### Version 1.0
- Initial plugin creation.

### Version 1.0.1
- Implmenting callback feature, added multistore support.

### Version 1.0.2
- Fixing refund callback which has no effect in order details.
- Fixing issue in the pricetag config which hiding anyday payment.
- Hide anyday payment option when order total > 30000.
- Fixing callback and admin web order comments consistency.
- Added configuration to change Pricetag language.
- Order confirmation emails are not getting sent.
- Anyday payment method's sort order was unable to configure.

### Version 1.0.3

- Fixed authentication with anyday account credentials.

### Version 1.0.4

- Fixed callback issue which appears when user authenticate using merchant account.

### Version 1.0.5

- Expired payment orders gets cancelled automatically by callback. 
