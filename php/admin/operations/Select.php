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
    * org
    */

    public function get_org_types()
    {
        $sql = "SELECT *
                FROM com_orgtype";

        $result = $this->dbcon->query($sql);

        return $result;
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
    //com_orgtype (orgtypeid,orgtypename,typetag,iconurl)
    public function get_orgs_of_an_user($userno)
    {
        $sql = "SELECT uo.orgno,
                    o.orgname,o.street, o.city,o.country, uo.moduleno,
                    (SELECT moduletitle FROM acc_modules WHERE moduleno = uo.moduleno) as moduletitle,
                    (SELECT orgtypename FROM com_orgtype WHERE orgtypeid in (SELECT orgtypeid FROM acc_orgs as org WHERE uo.orgno = org.orgno)) as orgtypename
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

}
