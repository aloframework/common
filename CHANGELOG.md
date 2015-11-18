# 1.3.3 #

The following happened to getUniqid():

Default $entropy value set to 10000, a warning is triggered if openssl_random_pseudo_bytes is unable to locate a 
cryptographically strong algorithm.

# 1.3.2 #

ENT_SUBSTITUTE added to Alo::unXss() 

# 1.3.1 #

Fixed a bug in getUniqid() which tried to use a nonexistent constant

# 1.3 #

getUniqid(), asciiRand(), isRegularRequest() added

# 1.2 #

Added unXss(), getFingerprint() and isTraversable()

# 1.1.1 #

Fixed a bug with isAjaxRequest()

# 1.1 #

`Alo::ifundefined()` added
