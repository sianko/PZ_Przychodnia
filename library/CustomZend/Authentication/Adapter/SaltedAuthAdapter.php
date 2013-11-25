<?php

namespace CustomZend\Authentication\Adapter;

use Zend\Authentication\Result as AuthenticationResult;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Sql;
use Zend\Db\Sql\Expression as SqlExpr;
use Zend\Db\Sql\Predicate\Operator as SqlOp;
use CustomZend\Crypt\Password\BcryptSaltVerification;
use Zend\Authentication\Adapter\DbTable\AbstractAdapter;

class SaltedAuthAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $saltColumn = 'salt';
    
    protected $credentialTreatment = null;

    /**
     * __construct() - Sets configuration options
     *
     * @param DbAdapter $zendDb
     * @param string    $tableName           Optional
     * @param string    $identityColumn      Optional
     * @param string    $credentialColumn    Optional
     * @param string    $saltColumn          Optional
     */
    public function __construct(
        DbAdapter $zendDb,
        $tableName = null,
        $identityColumn = null,
        $credentialColumn = null,
        $saltColumn = null
    ) {
        parent::__construct($zendDb, $tableName, $identityColumn, $credentialColumn);

        if (null !== $saltColumn) {
            $this->setSaltColumn($saltColumn);
        }
    }
    
    public function setSaltColumn($columnName)
    {
        $this->saltColumn = $columnName;
        return $this;
    }
    
    
    /**
     * _authenticateCreateSelect() - This method creates a Zend\Db\Sql\Select object that
     * is completely configured to be queried against the database.
     *
     * @return Sql\Select
     */
    protected function authenticateCreateSelect()
    {
        // get select
        $dbSelect = clone $this->getDbSelect();
        $dbSelect->from($this->tableName)
            ->where(new SqlOp($this->identityColumn, '=', $this->identity));

        return $dbSelect;
    }
    
    /**
     * _authenticateValidateResult() - This method attempts to validate that
     * the record in the resultset is indeed a record that matched the
     * identity provided to this adapter.
     *
     * Verification based on salted Bcrypt!
     *
     * @param  array $resultIdentity
     * @return AuthenticationResult
     */
    protected function authenticateValidateResult($resultIdentity)
    {
        if (!($resultIdentity[$this->saltColumn]) || empty($resultIdentity[$this->saltColumn])) {
            $this->authenticateResultInfo['code']       = AuthenticationResult::FAILURE;
            $this->authenticateResultInfo['messages'][] = 'Supplied salt (saltColumn) is invalid.';
            return $this->authenticateCreateAuthResult();
        }

        $bcrypt = new BcryptSaltVerification(array(
                                                'salt' => $resultIdentity[$this->saltColumn]
                                                ));
        
        if(($bcrypt->verify($this->credential, $resultIdentity[$this->credentialColumn])) === true)
        {
            $this->resultRow = $resultIdentity;

            $this->authenticateResultInfo['code']       = AuthenticationResult::SUCCESS;
            $this->authenticateResultInfo['messages'][] = 'Authentication successful.';
            return $this->authenticateCreateAuthResult();
        }
        
        $this->authenticateResultInfo['code']       = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
        $this->authenticateResultInfo['messages'][] = 'Supplied password is invalid.';
        return $this->authenticateCreateAuthResult();
        
    }
    
}
