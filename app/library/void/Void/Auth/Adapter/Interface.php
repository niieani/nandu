<?php

/**
 * Void
 *
 * LICENSE
 *
 * This source file is subject to the Simplified BSD License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://tekla.art.pl/license/void-simplified-bsd-license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to argasek@gmail.com so I can send you a copy immediately.
 *
 * @category   Void
 * @package    Void_Auth_Adapter
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Generic authentication adapter interface processing
 * credential data by some kind of treatment.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 */
interface Void_Auth_Adapter_Interface extends Zend_Auth_Adapter_Interface {

    /**
     * Get credential after being processed by some treatment
     * @return string
     */
    public function getTreatedCredential();

    /**
     * Set credential value
     * @param string $credential
     */
    public function setCredential($credential);

    /**
     * Set credential treatment object
     * @param Void_Auth_Credential_Treatment_Interface $treatment
     */
    public function setCredentialTreatment(Void_Auth_Credential_Treatment_Interface $treatment);

}