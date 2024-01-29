<?php
//session_start();
//include_once(dirname(__FILE__)."/Utility.php");

class Select
{
    private $dbcon;

    public function __construct($dbcon)
    {
        //$this->_validateUser();
        $this->dbcon = $dbcon;
    }


    /*
        * settings
        */

    public function get_all_account_types()
    {
        $sql = "SELECT *
                FROM acc_accounttype";

        $result = $this->dbcon->query($sql);

        return $result;
    }

    public function get_acctype_primary_accounts($acctypeno,$orgno)
    {
        $sql = "SELECT a.*,p.accname as praccname
                FROM acc_orgaccounthead as a
                    INNER JOIN acc_orgaccounthead as p ON a.praccno=p.accno
                WHERE a.orgno=? AND a.acctypeno=? AND
                        LEFT(a.accno, 2) > 0 AND RIGHT(a.accno,2) = 0";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("ii", $orgno, $acctypeno);
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }

    /*Get common accounts*/
    public function get_org_accounts_by_acctype($orgno, $acctypeno)
    {
        if ($acctypeno > 0) {
            $sql = "SELECT ca.*, pa.accname as praccname
                    FROM acc_orgaccounthead as ca
                        INNER JOIN acc_orgaccounthead as pa ON ca.praccno=pa.accno
                    WHERE pa.acctypeno=? AND ca.orgno=?
                    ";

            $stmt = $this->dbcon->prepare($sql);
            $stmt->bind_param("ii", $acctypeno, $orgno);
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();
        } else {
            $sql = "SELECT ca.*, pa.accname as praccname
                    FROM acc_orgaccounthead as ca
                        INNER JOIN acc_orgaccounthead as pa ON ca.praccno=pa.accno
                    WHERE ca.orgno=?";

            $stmt = $this->dbcon->prepare($sql);
            $stmt->bind_param("i", $orgno);
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();
        }
        return $result;
    }

    public function get_org_accounts_opening($orgno, $accyear)
    {
        $sql = "SELECT ca.*, pa.accname as praccname,j.debit, j.credit
                FROM (SELECT *
                    FROM acc_orgaccounthead
                    WHERE orgno=?) as ca
                    INNER JOIN acc_orgaccounthead as pa ON ca.praccno=pa.accno
                    LEFT JOIN ( SELECT orgno, accno, debit, credit
                                FROM acc_ledger
                                WHERE orgno=? AND transno =(
                                    SELECT transno
                                    FROM  acc_transaction
                                    WHERE orgno=? AND accyear=? AND ref='B/F - First Entry'
                                )) as j ON ca.accno=j.accno
                                ";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("iiis", $orgno, $orgno, $orgno, $accyear);
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();


        return $result;
    }

    public function get_common_accounts()
    {
        $prefix = constant('ACC_PREFIX');
        $table = "acc_commonaccount";

        $sql = "SELECT *
                FROM acc_commonaccount ";

        $result = $this->dbcon->query($sql);

        return $result;
    }

    /*Get currency*/
    public function get_currency()
    {
        $sql = "SELECT *
                FROM gen_currency";

        $result = $this->dbcon->query($sql);

        return $result;
    }

    /*Get accounts of an organizations*/
    public function get_org_accounts($orgno)
    {
        $sql = "SELECT *
                FROM acc_orgaccounthead
                WHERE orgno=?";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("i", $orgno);
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }

    /* Get primary accounts */
    public function get_primary_accounts()
    {

        $sql = "SELECT accno,accname as praccname
                FROM acc_orgaccounthead
                WHERE levelno<=2";

        $result = $this->dbcon->query($sql);

        return $result;
    }

    /*
        * org
        */

    public function get_org_types()
    {
        $sql = "SELECT *
                FROM acc_orgtype";

        $result = $this->dbcon->query($sql);

        return $result;
    }

    public function get_account_details_of_an_org($orgno)
    {
        $sql = "SELECT oa.accno, oa.accname, oa.levelno, oa.praccno,
                (SELECT accname as praccname FROM acc_orgaccounthead WHERE accno=oa.praccno) AS praccname
              FROM acc_orgaccounthead AS oa
              WHERE oa.orgno=? AND oa.praccno=4000 OR oa.praccno=5000 OR oa.praccno=6000";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("i", $orgno);
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }


    /*
        * accounting
        */

    public function get_total_debit_credit_of_accno_a_month_of_org($startdate, $lastdate, $orgno, $accno)
    {
        $sql = "SELECT accno,SUM(debit) as totaldebit, SUM(credit) as totalcredit
                FROM acc_ledger
                WHERE accno = ? AND transno IN
                            (SELECT transno
                            FROM acc_transaction
                            WHERE orgno=$orgno AND (tdate>=? AND tdate<=?))
                GROUP BY accno";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("iss", $accno, $startdate, $lastdate);
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }

    public function get_total_debit_credit_of_accno_by_month_of_org($orgno, $accno)
    {
        $sql = "SELECT accno,month(tdate) as monthno,SUM(debit) as totaldebit, SUM(credit) as totalcredit
                FROM acc_ledger as l
                    INNER JOIN acc_transaction as t ON l.transno=t.transno
                WHERE accno = ? AND l.orgno=$orgno
                GROUP BY accno,month(tdate)";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("i", $accno);
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }

    public function get_total_debit_credit_of_acctypeno_a_month_of_org($startdate, $lastdate, $orgno, $acctypeno)
    {
        $sql = "SELECT SUM(debit) as totaldebit, SUM(credit) as totalcredit
                FROM acc_ledger
                WHERE accno IN(SELECT accno
                                FROM acc_orgaccounthead
                                WHERE orgno=? AND acctypeno=?)
                        AND transno IN
                            (SELECT transno
                            FROM acc_transaction
                            WHERE orgno=? AND (tdate>=? AND tdate<=?))";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("iiiss", $orgno, $acctypeno, $orgno, $startdate, $lastdate);
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }

    public function get_total_debit_credit_of_an_accno_of_an_org($orgno, $accountno)
    {
        $prefix = constant('ACC_PREFIX');
        $table1 = "acc_ledger";
        $table2 = "acc_transaction";

        $sql = "SELECT accno, SUM(l.debit) AS totaldebit, SUM(l.credit) AS totalcredit
                FROM acc_ledger AS l
                WHERE l.accno=?
                    AND l.transno IN(
                            SELECT ts.transno
                            FROM acc_transaction AS ts
                            WHERE ts.orgno=?)
                GROUP BY accno";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("ii", $accountno, $orgno);
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }

    public function get_total_debit_credit_of_an_accno_of_an_org_of_an_accyear($orgno, $accountno, $accountingyear)
    {
        $sql = "SELECT accno, SUM(l.debit) AS totaldebit, SUM(l.credit) AS totalcredit
                FROM acc_ledger AS l
                WHERE l.accno=?
                    AND l.transno IN(
                            SELECT ts.transno
                            FROM acc_transaction AS ts
                            WHERE ts.accyear=? AND ts.orgno=?)
                GROUP BY accno";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("isi", $accountno, $accountingyear, $orgno);
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }

    //acc_accountingyear (orgno,accyear,startdate,closingdate,accyearstatus)
    public function get_current_accyear($orgno)
    {
        $sql = "SELECT accyear
                FROM acc_accountingyear
                WHERE startdate<=curdate() AND closingdate>=curdate()
                    AND orgno=? AND accyearstatus=1";

        $stmt = $this->dbcon->prepare($sql);
        if (!$stmt) {
            echo $this->dbcon->error;
        }

        $stmt->bind_param("i", $orgno);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $stmt->close();
                return $row["accyear"];
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }

    //acc_accountingyear (orgno,accyear,startdate,closingdate,accyearstatus)
    public function is_valid_accyear($dbcon, $orgno, $accyear, $accyearstatus=1)
    {
        $sql = "SELECT accyear
                FROM acc_accountingyear
                WHERE orgno=? AND accyear=? AND accyearstatus=?";

        $stmt = $dbcon->prepare($sql);
        if (!$stmt) {
            echo $dbcon->error;
        }

        $stmt->bind_param("isi", $orgno, $accyear,$accyearstatus);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }

    public function check_existance_of_running_accountingyear($orgno)
    {
        $sql = "SELECT  accyear
                FROM acc_accountingyear
                WHERE startdate<=curdate()
                    AND closingdate>=curdate()
                    AND orgno=?
                    AND  accyearstatus=1";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("i", $orgno);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $stmt->close();
                return 1;
            } else {
                $stmt->close();
                return 0;
            }
        } else {
            return -1;
        }
    }

    public function get_last_transaction_no()
    {
        $sql = "SELECT transno
                FROM  acc_transaction
                ORDER BY transno DESC
                LIMIT 1";

        $result = $this->dbcon->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            return $row['transno'] + 1;
        } else {
            return 1;
        }
    }

    public function get_receiptno_of_org_accyear($orgno, $accyear)
    {
        $sql = "SELECT receiptno
                FROM  acc_transaction
                WHERE orgno = ?
                    AND accyear = ?
                ORDER BY transno DESC
                LIMIT 1";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("is", $orgno,$accyear);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            return $row['receiptno'] + 1;
        } else {
            return 1;
        }
    }

    public function get_all_accounts_info_of_an_accounting_type_of_an_org($orgno, $accounttype)
    {
        $sql = "SELECT *,
                    (SELECT acctypedesc FROM acc_accounttype WHERE acctypeno = ?) as acctype
                FROM acc_orgaccounthead
                WHERE orgno=? AND acctypeno = ?";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("iii", $accounttype, $orgno, $accounttype);

        if (!$stmt->execute()) {
            $stmt->close();
            return false; //sql not executed
        } else {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
    }

    //acc_tbsupport (orgno,accno,accyear,debit,credit,addedby,updatetime)
    public function get_total_debit_credit_of_all_accounts_of_an_acctype_of_an_accyear_of_an_org($orgno, $accyear, $acctype)
    {
        $sql = "SELECT l.accno,(SELECT accname FROM acc_orgaccounthead as oa WHERE oa.accno=l.accno AND oa.orgno =  ?) as accname,
                    SUM(l.debit) AS totaldebit,SUM(l.credit) AS totalcredit
                FROM acc_ledger AS l
                WHERE l.accno IN(
                        SELECT accno
                        FROM acc_orgaccounthead
                        WHERE orgno = ? AND acctypeno=?)
                    AND l.transno IN(
                        SELECT ts.transno
                        FROM acc_transaction AS ts
                        WHERE ts.accyear=? AND ts.orgno=?)
                  GROUP BY l.accno order by l.accno";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("iiisi", $orgno, $orgno, $acctype, $accyear, $orgno);

        if (!$stmt->execute()) {
            return false;
        } else {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
    }

    //BE CAREFUL ABOUT ATTACK
    public function get_total_debit_credit_of_all_accounts_of_an_acctype_of_an_accyear_of_an_org_except_accounts($orgno, $accyear, $acctype, $excepts_accounts)
    {
        $sql = "SELECT l.accno,
                        (SELECT accname FROM acc_orgaccounthead as oa WHERE oa.accno=l.accno AND oa.orgno =  ?) as accname,
                        SUM(l.debit) AS totaldebit,SUM(l.credit) AS totalcredit
                  FROM acc_ledger AS l
                  WHERE l.accno IN(
                            SELECT accno
                            FROM acc_orgaccounthead
                            WHERE orgno = ? AND acctypeno=?)
                        AND l.accno not in($excepts_accounts)
                        AND l.transno IN(
                            SELECT ts.transno
                            FROM acc_transaction AS ts
                            WHERE ts.accyear=? AND ts.orgno=?)
                  GROUP BY l.accno order by l.accno";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("iiisi", $orgno, $orgno, $acctype, $accyear, $orgno);

        if (!$stmt->execute()) {
            return false;
        } else {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
    }

    public function get_budget_accounts_derived_from_accounting_year($orgno, $accyear)
    {
        $sql = "SELECT a.accno, a.accname, a.levelno,a.praccname, IFNULL(l.totaldebit,0) as totaldebit, IFNULL(l.totalcredit,0) as totalcredit
                FROM (SELECT oa.accno, oa.accname
                    FROM acc_orgaccounthead as oa
                    WHERE oa.orgno=? AND pa.acctypeno IN(4000,6000)) as a
                    LEFT JOIN
                        (SELECT accno, SUM(debit) AS totaldebit, SUM(credit) AS totalcredit
                        FROM acc_ledger
                        WHERE orgno=? AND transno in(SELECT transno
                                                    FROM acc_transaction
                                                    WHERE orgno=? AND accyear=? AND confirmed='Y')
                        GROUP BY accno) as l ON a.accno=l.accno
                    order by l.accno";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("iiis", $orgno, $orgno, $orgno, $accyear);

        if (!$stmt->execute()) {
            return false;
        } else {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
    }

    public function get_budget_expense_income_accounts_derived_from_accounting_year($orgno, $accyear)
    {
        $sql = "SELECT a.accno, a.accname, a.levelno,a.acctypeno, a.praccname,
                        IFNULL(l.totaldebit,0) as totaldebit, IFNULL(l.totalcredit,0) as totalcredit
                FROM (SELECT oa.accno, oa.accname, oa.acctypeno,oa.levelno,
                            (SELECT accname FROM acc_orgaccounthead WHERE accno=oa.praccno) as praccname
                    FROM acc_orgaccounthead as oa
                    WHERE oa.orgno=? AND oa.praccno IS NOT NULL AND oa.acctypeno IN(3000, 4000)) as a
                    LEFT JOIN
                        (SELECT accno, SUM(debit) AS totaldebit, SUM(credit) AS totalcredit
                        FROM acc_ledger
                        WHERE orgno=? AND transno in(SELECT transno
                                                    FROM acc_transaction
                                                    WHERE orgno=? AND accyear=? AND confirmed='Y')
                        GROUP BY accno) as l ON a.accno=l.accno
                    order by l.accno";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("iiis", $orgno, $orgno, $orgno, $accyear);

        if (!$stmt->execute()) {
            return false;
        } else {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
    }

    public function get_an_acc_type_info($acctype)
    {
        $sql = "SELECT *
                FROM acc_accounttype
                WHERE acctypeno=?";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("i", $acctype);

        if (!$stmt->execute()) {
            return false;
        } else {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
    }

    public function get_all_accyear_of_an_org($orgno)
    {
        $sql = "SELECT accyear,  accyearstatus, startdate, closingdate
                FROM acc_accountingyear
                WHERE orgno=?
                order by accyear desc";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("i", $orgno);

        if (!$stmt->execute()) {
            return false;
        } else {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
    }

    public function get_fixedassets_to_depriciate($orgno, $date)
    {
        $sql = "SELECT  assetno , assetname , pricerate , qty , assetacc , depriciationstartdate , lifeyear , lastdatedepriciated ,residualvalue,
                  datediff(?, lastdatedepriciated ) as daysfromlastdepriciation,daysdepriciated,lifedays
                  FROM
                  (SELECT *,datediff(lastdatedepriciated,depriciationstartdate) as daysdepriciated, (lifeyear*365) as lifedays FROM fixedasset WHERE orgno = ?) as fa
                  WHERE daysdepriciated<lifedays";

        $stmt = $this->dbcon->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("si", $date, $orgno);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $stmt->close();
                return $result;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }

    /*
    * User
    */

    //gen_userstatus (userstatusno,userstatustitle)
    public function get_user_status()
    {
        $sql = "SELECT *
                FROM gen_userstatus";

        $result = $this->dbcon->query($sql);

        return $result;
    }
    //Rasel
    /*
    * Get a particular user
    */

    public function check_user_existence($username)
    {
        $sql = "SELECT userno
                FROM gen_users
                WHERE username=?";
        $stmt = $this->dbcon->prepare($sql);

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    /**
     * Get all items of an organization of a specific category
     */

     //acc_userorgmodules(orgno,userno,moduleno,verified)
     //acc_modules(moduleno,moduletitle)
     //gen_users (userno,firstname,lastname,email,countrycode,contactno,username,passphrase,authkey,userstatusno)
     //gen_userstatus (userstatusno,userstatustitle)
    public function get_users_of_an_org($orgno)
    {
        $sql = "SELECT uo.orgno, uo.userno, u.username, u.firstname, u.lastname, u.countrycode,u.contactno,
                      u.userstatusno,(SELECT userstatustitle FROM gen_userstatus WHERE userstatusno=u.userstatusno) AS userstatustitle,
                      uo.moduleno,(SELECT moduletitle FROM acc_modules WHERE moduleno=uo.moduleno) AS moduletitle,
                      uo.verified
                FROM
                      (SELECT *
                      FROM acc_userorgmodules
                      WHERE orgno=?) AS uo
                    INNER JOIN gen_users AS u ON uo.userno=u.userno";

        //$sql = "SELECT reg.fullname, reg.contactno, reg.vill, reg.ps, reg.po, reg.district FROM " . $table . " AS reg WHERE orgno=?";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("i", $orgno);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();

        return $results;
    }
    /*
         * Check adminship of logged in user
         */
    public function check_module_of_current_user($orgno, $userno)
    {
        $sql = "SELECT moduleno
                FROM acc_userorgmodules
                WHERE orgno=? AND userno=?";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("ii", $orgno, $userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $meta_array = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $meta_array[] = $row;
            }
            /*$response['data'] = $meta_array;
                $row = $response['data'];
                echo json_encode($row[0]['roleid']);*/
            if ($meta_array[0]['roleid'] == 1) {
                return true;
            }
        }
        return false;
    }
    /*
         * get user ID
         */
    public function get_userno($username)
    {
        $sql = "SELECT userno
                FROM gen_users
                WHERE username=?";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $meta_array = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $meta_array[] = $row;
            }
            return $meta_array[0]['userno'];
        } else {
            return -1;
        }
    }

    /*
         * get verification status
         */
    public function get_user_verification($userno)
    {
        $sql = "SELECT verified
                FROM acc_userorgmodules
                WHERE userno=?";
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("i", $userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $meta_array = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $meta_array[] = $row;
            }
            return $meta_array[0]['verified'];
        } else {
            return "";
        }
    }

    /*
        * get user info
        */

    //gen_users (userno,firstname,lastname,email,countrycode,contactno,username,passphrase,authkey,userstatusno)
    public function get_an_user_info($username)
    {

        $sql = "SELECT userno,username,firstname,lastname,email,countrycode,contactno,passphrase,authkey
                FROM gen_users
                WHERE username=? AND userstatusno=1";

        $stmt = $this->dbcon->prepare($sql);
        //echo "";
        if ($stmt) {
            $stmt->bind_param("s", $username);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $stmt->close();
                return $result;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            $stmt->close();
            return false;
        }
    }

    public function get_an_user_info_by_id($userno)
    {
        $prefix = constant('ACC_PREFIX');
        $table = $prefix . "registration";

        $sql = "SELECT  username ,  fullname ,  contactno ,  vill ,  ps ,  po ,  postcode ,  district
                FROM $table
                WHERE userno=?";

        $stmt = $this->dbcon->prepare($sql);
        //echo "";
        if ($stmt) {
            $stmt->bind_param("i", $userno);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $stmt->close();
                return $result;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            $stmt->close();
            return false;
        }
    }

    public function get_an_user_all_info_by_id($userno)
    {
        $prefix = constant('ACC_PREFIX');
        $table = $prefix . "registration";

        $sql = "SELECT * FROM $table WHERE userno=?";

        $stmt = $this->dbcon->prepare($sql);
        //echo "";
        if ($stmt) {
            $stmt->bind_param("i", $userno);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $stmt->close();
                return $result;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            $stmt->close();
            return false;
        }
    }

    public function get_info_of_an_org_of_user($userno, $orgno)
    {
        $sql = "SELECT *,
                    (SELECT orgname FROM acc_orgs WHERE orgno=uo.orgno) as orgname,
                    (SELECT concat(street, ', ', city) FROM acc_orgs as o WHERE uo.orgno = o.orgno) as orgaddress
                FROM acc_userorgmodules as uo
                WHERE userno=?
                    AND orgno=?
                    AND verified='Y'";

        $stmt = $this->dbcon->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $userno, $orgno);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $stmt->close();
                return $result;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }

    //acc_userorgmodules(orgno,userno,moduleno,verified)
    //acc_modules(moduleno,moduletitle)
    //acc_orgtype (orgtypeid,orgtypename,typetag,iconurl)
    public function get_orgs_of_an_user($userno)
    {
        $sql = "SELECT uo.orgno,
                    o.orgname,o.street, o.city,o.country, uo.moduleno,
                    (SELECT moduletitle FROM acc_modules WHERE moduleno = uo.moduleno) as moduletitle,
                    (SELECT orgtypename FROM acc_orgtype WHERE orgtypeid in (SELECT orgtypeid FROM acc_orgs as org WHERE uo.orgno = org.orgno)) as orgtypename
                FROM acc_userorgmodules as uo
                    INNER JOIN (SELECT orgno, orgname,street,city,country
                                FROM acc_orgs
                                WHERE verifiedno=1) as o ON o.orgno=uo.orgno
                WHERE userno=?";
        //echo $sql;
        $stmt = $this->dbcon->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $userno);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $stmt->close();
                return $result;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }

    public function get_user_modules($userno)
    {
        $sql = "SELECT moduleno
                FROM  acc_userorgmodules
                WHERE userno=?";

        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("i", $userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Report Functions
     * Dev. Hanif
     * 24 June 2023
     */
    public function get_levelwise_statements($orgno,$levelno,$acctypeno,$accyear){
        $sql = "SELECT tb.accno,tb.accyear,tb.debit as totaldebit,tb.credit as totalcredit,
                        oa.accname, ty.opat
                FROM (SELECT accno,accyear,debit,credit
                        FROM acc_tbsupport
                        WHERE orgno=? AND accyear=?
                        ) as tb
                    INNER JOIN (SELECT accno, accname, acctypeno
                                FROM acc_orgaccounthead
                                WHERE levelno=?
                                AND acctypeno=?) as oa ON tb.accno=oa.accno
                    INNER JOIN acc_accounttype as ty ON oa.acctypeno=ty.acctypeno
                    INNER JOIN (SELECT orgno, accyear, startdate, closingdate
                                FROM acc_accountingyear
                                WHERE orgno=? AND accyear=?) as ac";
        // echo $sql;
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("isiiis", $orgno, $accyear,$levelno, $acctypeno,$orgno, $accyear);
        $stmt->execute();
        $result=$stmt->get_result();
        $stmt->close();
        return $result;
    }

    //acc_tbsupport (orgno,accno,accyear,debit,credit,addedby,updatetime)
    public function get_acctype_statement_summary($orgno,$levelno,$acctypeno,$accyear){
        $sql = "SELECT tb.accyear,oa.acctypeno,sum(tb.debit) as totaldebit,sum(tb.credit) as totalcredit
                FROM (SELECT accno,accyear,debit,credit
                        FROM acc_tbsupport
                        WHERE orgno=? AND accyear=?
                        ) as tb
                    INNER JOIN (SELECT accno, accname, acctypeno
                                FROM acc_orgaccounthead
                                WHERE levelno=?
                                AND acctypeno=?) as oa ON tb.accno=oa.accno
                GROUP BY tb.accyear,oa.acctypeno";
        // echo $sql;
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("isii", $orgno, $accyear,$levelno, $acctypeno);
        $stmt->execute();
        $result=$stmt->get_result();
        $stmt->close();
        return $result;
    }

    //acc_transaction (transno,orgno,accyear,vouchertype,receiptno,ref,tdate,confirmed,addedby,entrydatetime,narration)
    //acc_ledger (ledgerserial,transno,orgno,accno,lnote,debit,credit,particular)
    public function get_range_instant_statement($orgno,$accyear,$startdate, $lastdate,$acctypeno){
        $sql = "SELECT IFNULL(sum(debit),0) as totaldebit,IFNULL(sum(credit),0) as totalcredit
                FROM (SELECT accno,debit,credit
                        FROM acc_ledger
                        WHERE transno IN(
                                SELECT DISTINCT transno
                                FROM acc_transaction
                                WHERE orgno=?
                                    AND accyear=?
                                    AND tdate BETWEEN ? AND ?
                                    AND confirmed=1
                                )
                            AND accno IN (
                                SELECT accno
                                FROM acc_orgaccounthead
                                WHERE acctypeno=?
                                )
                        ) as l";
        // echo $sql;
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("isssi", $orgno, $accyear, $startdate, $lastdate,$acctypeno);
        $stmt->execute();
        $result=$stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function get_instant_acctype_balance($orgno,$accyear,$acctypeno){
        $sql = "SELECT IFNULL(sum(debit),0) as totaldebit,IFNULL(sum(credit),0) as totalcredit
                FROM (SELECT accno,debit,credit
                        FROM acc_ledger
                        WHERE transno IN(
                                SELECT DISTINCT transno
                                FROM acc_transaction
                                WHERE orgno=?
                                    AND accyear=?
                                    AND confirmed=1
                                )
                            AND accno IN (
                                SELECT accno
                                FROM acc_orgaccounthead
                                WHERE acctypeno=?
                                )
                        ) as l";
        // echo $sql;
        $stmt = $this->dbcon->prepare($sql);
        $stmt->bind_param("isi", $orgno, $accyear, $acctypeno);
        $stmt->execute();
        $result=$stmt->get_result();
        $stmt->close();
        return $result;
    }

}
