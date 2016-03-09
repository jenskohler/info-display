<?php
/* (c) 2014 Thomas Smits */
namespace HSMA\InfoDisplay\Entity\Security;

/**
 * Class PasswordChange
 * @package HSMA\InfoDisplay\Entity\Security
 *
 * Entity for the password change form.
 */
class PasswordChange {

    /**
     * @var string old password
     */
    private $oldPassword;

    /**
     * @var string new password
     */
    private $newPassword;

    /**
     * @var string new password repeated
     */
    private $newPasswordRepeat;

    /**
     * @return string the new password
     */
    public function getNewPassword() {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword the new password
     */
    public function setNewPassword($newPassword) {
        $this->newPassword = $newPassword;
    }

    /**
     * @return string the new password repeated
     */
    public function getNewPasswordRepeat() {
        return $this->newPasswordRepeat;
    }

    /**
     * @param string $newPasswordRepeat the new password repeated
     */
    public function setNewPasswordRepeat($newPasswordRepeat) {
        $this->newPasswordRepeat = $newPasswordRepeat;
    }

    /**
     * @return string the old password
     */
    public function getOldPassword() {
        return $this->oldPassword;
    }

    /**
     * @param string $oldPassword the old password
     */
    public function setOldPassword($oldPassword) {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return bool true if both new passwords match
     */
    public function getPasswordsMatch() {
        return $this->newPassword === $this->newPasswordRepeat;
    }
}
