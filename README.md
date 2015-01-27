PHP SDK for Face++
================
1. Register user on http://www.faceplusplus.com. 

2. Create an App on DevCenter to obtain API_KEY & API_SECRET. 

3. Open facepp_sdk.php.

4. If you choose Amazon(US) server, please uncomment the 'http://apius.faceplusplus.com/v2' or 'https://apius.faceplusplus.com/v2' in line 21 - 24

5. Add the API_KEY & API_SECRET to be a standard in line 27 & 28 or assign them dynamicly to your object.
     
        <?php
            $facepp = new Facepp();
            $facepp->api_key    = '{YOUR_KEY_HERE}';
            $facepp->api_secret = '{YOUR_SECRET_HERE}';
        ?>
