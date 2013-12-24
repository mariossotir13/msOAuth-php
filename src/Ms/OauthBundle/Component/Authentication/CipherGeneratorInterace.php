<?php

namespace Ms\OauthBundle\Component\Authentication;

/**
 * Κρυπτογραφεί και αποκρυπτογραφεί συμβολοσειρές χρησιμοποιώντας ένα δεδομένο
 * κλειδί.
 * 
 * Δεν υπάρχει περιορισμός για το είδος της κρυπτογράφησης. Με άλλα λόγια, η
 * μέθοδος μπορεί να είναι είτε συμμετρική είτε ασύμμετρη.
 * 
 * @author Marios
 */
interface CipherGeneratorInterace {
    
    /**
     * Αποκρυπτογραφεί ένα κρυπτόλεξο χρησιμοποιώντας ένα κλειδί.
     * 
     * @param string $ciphertext
     * @param string $key
     * @return string Η αναπαράσταση απλού κειμένου της παραμέτρου `$ciphertext`.
     */
    public function decrypt($ciphertext, $key);
    
    /**
     * Κρυπτογραφεί μία συμβολοσειρά χρησιμοποιώντας ένα κλειδί.
     * 
     * @param string $plaintext
     * @param string $key
     * @return string Το κρυπτόλεξο της παραμέτρου `$plaintext`.
     */
    public function encrypt($plaintext, $key);
}