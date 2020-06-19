<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/5
 * Time: 9:46
 * git https://github.com/CraryPrimitiveMan/simple-framework/blob/0.5/src/db/ModelInterface.php
 */
namespace Base\Interfaces;
    /**
     * ModelInterface
     * @author Harry Sun <sunguangjun@126.com>
     */
interface ModelInterface
{
    /**
     * Declares the name of the database table associated with this Model class.
     * @return string the table name
     */
    public static  function tableName();
    /**
     * Returns the primary key **name(s)** for this Model class.
     * @return string[] the primary key name(s) for this Model class.
     */
    /**
     * Returns a single model instance by a primary key or an array of column values.
     *
     * ```php
     * // find the first customer whose age is 30 and whose status is 1
     * $customer = Customer::findOne(['age' => 30, 'status' => 1]);
     * ```
     *
     * @param mixed $condition a set of column values
     * @return static|null Model instance matching the condition, or null if nothing matches.
     */
    public  function findOne();
    /**
     * Returns a list of models that match the specified primary key value(s) or a set of column values.
     *
     *  ```php
     * // find customers whose age is 30 and whose status is 1
     * $customers = Customer::findAll(['age' => 30, 'status' => 1]);
     * ```
     *
     * @param mixed $condition a set of column values
     * @return array an array of Model instance, or an empty array if nothing matches.
     */
    public  function findAll();
    /**
     * Updates models using the provided attribute values and conditions.
     * For example, to change the status to be 2 for all customers whose status is 1:
     *
     * ~~~
     * Customer::updateAll(['status' => 1], ['status' => '2']);
     * ~~~
     *
     * @param array $attributes attribute values (name-value pairs) to be saved for the model.
     * @param array $condition the condition that matches the models that should get updated.
     * An empty condition will match all models.
     * @return integer the number of rows updated
     */
    public  function save();
    /**
     * Deletes models using the provided conditions.
     * WARNING: If you do not specify any condition, this method will delete ALL rows in the table.
     *
     * For example, to delete all customers whose status is 3:
     *
     * ~~~
     * Customer::deleteAll([status = 3]);
     * ~~~
     *
     * @param array $condition the condition that matches the models that should get deleted.
     * An empty condition will match all models.
     * @return integer the number of rows deleted
     */
    public function delete($condition);

}
