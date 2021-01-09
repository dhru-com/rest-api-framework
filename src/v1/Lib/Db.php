<?php
/*
 * V1.2
 */

namespace Dhru\Lib;


class Db extends \PDO
{


    public function deleteData(string $table,string $where,bool $harddelete=false)
    {
        global $db;

        if($harddelete){
            self::deletedData($table,$where);

            if($db->query("DELETE FROM  $table WHERE $where"))
            {
                return true;
            }else{
                return false;
            }
        }else{

            if($db->query("UPDATE  $table SET `del`=1 WHERE $where"))
            {
                return true;
            }else{
                return false;
            }
        }

    }

    function deletedData($table,$where)
    {

        global $db;

        $que=$db->query("SELECT * FROM $table WHERE $where");
        $row=$que->fetch(\PDO::FETCH_ASSOC);

        $db->query("delete from $table where $where");
        $DATA['deleteddata']=json_encode($row);
        $DATA['deletedtable']=$table;
        $DATA['deletedcondition']=$where;
        $DATA['deletedtime']=time();
        $DATA['deleteddate']=date("Y-m-d");
        //self::insertData($DATA,"tblDeleted");
    }

    static function insertData(array $DATA=[],string $table)
    {
        global $db;

        $Field="";
        $Value="";
        $BindData = [];
        foreach($DATA as $Key=>$Val){
            // $Val = mysqli_real_escape_string($Val);
            $Field.="`$Key`,";
            $Value.=":$Key,";
            $BindData[":$Key"] = $Val;
        }
        $Field=rtrim($Field,",");
        $Value=rtrim($Value,",");
        $sql=$db->prepare("INSERT INTO $table ($Field)  VALUE ($Value)");
        $sql->execute($BindData);
        return $db->lastInsertId();
    }

    static function queryPagination($query,$Base){

        global $db;

        $limit=$Base->querystring['endlimit'];
        $offset=$Base->querystring['startlimit'];

        $query=$query." LIMIT $offset,$limit";
        preg_match("/FROM\s(.*)\sLIMIT/", $query, $matches1);

        $querycount=$matches1[1];
        $querycount="SELECT SQL_NO_CACHE * FROM $querycount";

        $RETURN['total']=$db->query($querycount)->rowCount();

        $RETURN['queryobj']=$db->query($query);

        return $RETURN;
    }
    static function selectDataNew(array $fields,string $table ,array $where , $fatchAll = false , $order_by= '',$Start=0,$Limit=10000) {
        global $db;

        $Field= $_w = "";
        $BindData=[];
        //$_where=$db->buildWhere($where);
        //////////BUILD QUERY WHERE////
        $_w = " WHERE ";
//print_r($where);

        //$BindData[":$key"] = $_val;
        //foreach()
        $BindData = $_where[1];
        ///////END BUILD QUERY WHERE//
        if($_ENV['pagelimit']){
            $Limit=$_ENV['pagelimit'];
        }
        $page='';
        if($_ENV['page']>0){
            $page=$_ENV['page'];
        }
        if($page!=''){
            $page=$page-1;
            $Start=$page*$Limit;
        }



        foreach($fields as $Key=>$Val){
            //$Val = mysqli_real_escape_string($Val);
            if(preg_match('/^[^ ].* .*[^ ]$/', $Val)){
                $Field .= "$Val ,";
            }elseif(is_string($Key)){
                $Field .= "`$Key` ,";
            }elseif(is_int($Key) && is_string($Val)) {
                if($Val==='*'){
                    $Field .= "$Val ,";
                }else{
                    $Field .= "`$Val` ,";
                }
            }elseif(is_string($Key) && is_string($Val)) {
                $Field .= "`$Val` AS `$Val` ,";
            }
        }

        $Field=rtrim($Field,',');
        $sql = $db->prepare("SELECT $Field FROM $table $_w $order_by LIMIT $Start,$Limit");
        $sql->execute($BindData);

        $sql2=$db->prepare("SELECT $Field FROM $table $_w");
        $sql2->execute($BindData);


        $totalRows = $sql2->rowCount();
        $OUT['total']=$totalRows;
        if($fatchAll){
            $Val= $sql->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $OUT['data']=$Val;
            return $OUT;
        }else{

            $Val= $sql->fetch(\PDO::FETCH_ASSOC);
            $sql->closeCursor();
            $OUT['data']=$Val;
            return $OUT;
        }

    }

    static function selectData(array $fields,string $table ,array $where , $fatchAll = false , $order_by= '',$Start=0,$Limit=10000) {
        global $db;

        $Field= $_w = "";
        $BindData=[];

        $_where=$db->buildWhere($where);
        if($_ENV['pagelimit']){
            $Limit=$_ENV['pagelimit'];
        }
        $page='';
        if($_ENV['page']>0){
            $page=$_ENV['page'];
        }
        if($page!=''){
            $page=$page-1;
            $Start=$page*$Limit;
        }

        if($_where) {
           $_w = $_where[0];
           $BindData = $_where[1];
        }


        foreach($fields as $Key=>$Val){
            //$Val = mysqli_real_escape_string($Val);
            if(preg_match('/^[^ ].* .*[^ ]$/', $Val)){
                $Field .= "$Val ,";
            }elseif(is_string($Key)){
                $Field .= "`$Key` ,";
            }elseif(is_int($Key) && is_string($Val)) {
                if($Val==='*'){
                    $Field .= "$Val ,";
                }else{
                    $Field .= "`$Val` ,";
                }
            }elseif(is_string($Key) && is_string($Val)) {
                $Field .= "`$Val` AS `$Val` ,";
            }
        }

        $Field=rtrim($Field,',');
        $sql = $db->prepare("SELECT $Field FROM $table $_w $order_by LIMIT $Start,$Limit");
        $sql->execute($BindData);

        $sql2=$db->prepare("SELECT $Field FROM $table $_w");
        $sql2->execute($BindData);


        $totalRows = $sql2->rowCount();
        $OUT['total']=$totalRows;
        if($fatchAll){
            $Val= $sql->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $OUT['data']=$Val;
            return $OUT;
        }else{

            $Val= $sql->fetch(\PDO::FETCH_ASSOC);
            $sql->closeCursor();
            $OUT['data']=$Val;
            return $OUT;
        }

    }

    static function updateData(array $DATA=[],string $table,array $where ){
        global $db;


        $Field="";
        $BindData = [];


        $_where = $db->buildWhere($where);
        if($_where===''){
            return false;
        }


        $_w = $_where[0];
        $BindData = $_where[1];

        foreach($DATA as $Key=>$Val){
            //$Val = mysqli_real_escape_string($Val);

            $Field.="`$Key`=:$Key,";
            $BindData[":$Key"] = $Val;
        }
        $Field=rtrim($Field,',');

        $sql=$db->prepare("UPDATE $table set $Field $_w");
        $sql->execute($BindData);


        if($rowCount  = $sql->rowCount()){
            return  $rowCount;
        }else{
            return false;
        }
    }
    static function whereDetail(string $_w,string $k,array $value){
        global $db;


        if($value[0] ==='NEQ'){
            $_w .= " (`$k`!=:$k) ";
            $_val = $value[1]? $value[1] : '';
        }else if($value[0] ==='LIKE'){
            $_w .= " (`$k` LIKE :$k) ";
            $_val = $value[1]? $value[1] : '';
        }else if($value[0] ==='START'){
            $_w .= " (`$k` LIKE :$k) ";
            $_val = ($value[1]? $value[1] : '').'%';
        }else if($value[0] ==='END'){
            $_w .= " (`$k` LIKE :$k) ";
            $_val = '%'.($value[1]? $value[1] : '');
        }else if($value[0] ==='CONTAIN'){
            $_w .= " (`$k` LIKE :$k) ";
            $_val = '%'.($value[1]? $value[1] : '').'%';
        }else if($value[0] ==='NCONTAIN'){
            $_w .= " (`$k` NOT LIKE :$k) ";
            $_val = '%'.($value[1]? $value[1] : '').'%';
        }else if($value[0] ==='GREATER'){
            $_w .= " (`$k` > :$k) ";
            $_val = "$value[1]";
        }else if($value[0] ==='LESS'){
            $_w .= " (`$k` < :$k) ";
            $_val = $value[1]? $value[1] : '';
        }else if($value[0] ==='BETWEEN'){
            $_w .= " (`$k` between $value[1]) ";
            $_val='';
        }else if($value[0] ==='IN'){
            $_w .= " (`$k` IN  ($value[1])) ";
           // $_val = $value[1]? $value[1] : '';
            $_val='';
        }else if($value[0] ==='GT'){
            $ok=rtrim($k,'1');
            $_w .= " (`$ok`>= :$k) ";
            $_val=$value[1];
        }else if($value[0] ==='LT'){
            $ok=rtrim($k,'2');
            $_w .= " (`$ok`<= :$k) ";
            $_val=$value[1];
        }else if($value[0] ==='EQ'){
            $_w .= " (`$k`=:$k) ";
            $_val = $value[1];
        }else {
            $_w .= " (`$k`=:$k) ";
            $_val = $value[0];
        }

        return array($_w,$_val);
    }

    static function buildWhere(array $where){
        global $db;



        if ($where and count($where) > 0) {

            $_w = ' WHERE ';
            $BindData = [];

            if($where['mainlogic']){

                $_w.=" (";

                    foreach($where['sub'] as $condition){
                        $_w.=" (";

                        foreach($condition['column'] as $k=>$coldeta){
                            $value = explode(':', $coldeta);
                            $_val = '';
                            $Res=$db->whereDetail($_w,$k,$value);
                            $_w=$Res[0];
                            $_val=$Res[1];

                            $BindData[":$k"] = $_val;
                            $_w.=" $condition[logic]";

                        }

                        $_w=rtrim($_w,$condition[logic]);
                        $_w.=" ) ";
                        $_w.=" $where[mainlogic]";
                    }
                $_w=rtrim($_w,$where['mainlogic']);
                $_w.=" )  ";
                $_w.=" $where[mainlogic]";
                foreach($where as  $key=>$deta){
                    if($key!='sub' && $key!='mainlogic'){
                            $value = explode(':', $deta);
                            $_val = '';
                            $Res = $db->whereDetail($_w, $key, $value);

                            $_w = $Res[0] . " " . $where['mainlogic'];
                            $_val = $Res[1];
                            $BindData[":$key"] = $_val;

                    }
                }
                 $_w=rtrim($_w,$where['mainlogic']);
            }else{
                foreach ($where as $k => $value) {
                    $value = explode(':', $value);
                    $_val = '';
                    $Res=$db->whereDetail($_w,$k,$value);
                    $_w=$Res[0]." AND";
                    $_val=$Res[1];

                    $BindData[":$k"] = $_val;
                }
                $_w=rtrim($_w,'AND');
            }
            return [$_w,$BindData];

        }
        return '';
    }
}

