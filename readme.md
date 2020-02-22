# QIF Transformer

QIF Transformer can transform transaction data output from various Australian financial institution websites into QIF format.

Currently it supports:
* CitiBank Credit Card statements (`citi`)
    * Download the PDF statements and copy the lines into a text file. Each line should look like:
    ```
    Dec 01 Purchase Description 1234567890123456 100.00
    ```
* Kogan Money (`kogan`)
    * Log onto the online portal, and open the DevTools Network Tab, filtered to XHR requests. Look for a request to:
    `https://aspac.api.citi.com/gcb/api/v1/accounts/<long account ID>/transactions`
    * Save this JSON to a file, and feed it into the script.

# Usage
Install PHP dependencies with `composer install`.

Then run `php index.php filename sourcename`.

See sourcenames above or examine `index.php` for more details and options.
