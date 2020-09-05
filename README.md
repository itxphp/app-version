# installing
`composer require itx-utilities\app-version`
--- 
# usage
```
<?php
use \Itx\Utilities\AppVersion ;
use \Itx\Utilities\AppVersion\Exceptions\AppVersionException ;

try
{
    // to get android app latest published version ;
    $android = AppVersion::android("net.tsawq.apps") ;
    // to get ios app latest published version ;
    // accepts both id , or bundleId 
    $ios = AppVersion::ios("1355111")  ;
} catch(\AppVersionException $e) {
    // handle any errors 
}
```