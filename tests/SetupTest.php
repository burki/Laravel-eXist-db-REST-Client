<?php namespace ExistDbRestClient;

/**
 * Class SetupTest
 * @package ExistDbRestClient
 */
class SetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExistDbRestClient
     */
    public static $connection = null;

    /**
     * @var array
     */
    public static $config;

    /**
     * @var string
     */
    public static $collectionName = "CDCatalog";

    public static function setupBeforeClass()
    {
        self::$config = array(
            'uri' => 'http://localhost:8080/exist/rest/',

            'user' => 'admin',
            'password' => 'admin',

            'xsl' => 'no',
            'indent' => 'yes',
            'howMany' => 0,
            'start' => 1,
            'wrap' => 'yes'
        );

        self::$connection = new ExistDbRestClient(self::$config);
    }
/*
    public static function tearDownAfterClass()
    {
        $query = self::$connection->prepareQuery();
        $query->setCollection(self::$collectionName);
        try {
            $query->delete();
        } catch (\Exception $e) {

        }
    }
*/
    public function tearDown() {
        echo "Time elapsed: " . $this->getTestResultObject()->time(). PHP_EOL;
    }

    public function testTrue() {
        $this->assertTrue(1 == 1);
    }

    protected function insertData()
    {
        $catalogAsSingleNode = simplexml_load_file(__DIR__ . '/xml/cd_catalog.xml');
        foreach ($catalogAsSingleNode->children() as $child) {
            $md5able = '';
            foreach ($child->children() as $property) {
                $md5able .= (string)$property;
            }

            $query = self::$connection->prepareQuery();
            $query->setCollection(self::$collectionName);
            $query->setResource(md5($md5able) . '.xml');
            $query->setBody($child->asXML());
            $query->put();
        }

        $this->assertTrue(true);
    }

    protected function findArrayByValueName($arr, $name, $namespace = "{}") {
        $value = $arr['value'];

        foreach($value as $v) {
            if($v['name'] == $namespace . $name) {
                return $v;
            }
        }

        return null;
    }
}