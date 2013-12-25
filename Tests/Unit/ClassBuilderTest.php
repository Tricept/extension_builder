<?php
namespace EBT\ExtensionBuilder\Tests\Unit;
/***************************************************************
 *  Copyright notice
 *
 * (c) 2010 Nico de Haen
 * All rights reserved
 *
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use EBT\ExtensionBuilder\Utility\Inflector;


/**
 *
 * @author ndh
 *
 */
class ClassBuilderTest extends \EBT\ExtensionBuilder\Tests\BaseTest {

	var $modelName = 'Model1';

	function setUp() {
		parent::setUp();
		$this->generateInitialModelClassFile($this->modelName);
	}

	public function tearDown() {
		$this->removeInitialModelClassFile($this->modelName);
	}

	/**
	 * @test
	 */
	public function classBuilderGeneratesSetterMethodForSimpleProperty() {
		$domainObject = $this->buildDomainObject($this->modelName, true, true);

		$property0 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty('name');
		$domainObject->addProperty($property0);

		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject, TRUE);

		$this->assertTrue(is_object($modelClassObject), 'No model class object');
		$this->assertTrue($modelClassObject->methodExists('setName'), 'No method: setName');

		$setNameMethod = $modelClassObject->getMethod('setName');
		$parameters = $setNameMethod->getParameters();
		$this->assertEquals(count($parameters), 1);
		$firstParameter = array_shift($parameters);
		$this->assertEquals($firstParameter->getName(), 'name');
	}


	/**
	 *
	 */

	public function classBuilderGeneratesGetterMethodForSimpleProperty() {

		$domainObject = $this->buildDomainObject($this->modelName, true, true);
		$property0 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty('name');
		$property0->setRequired(TRUE);
		$domainObject->addProperty($property0);

		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject, TRUE);
		$this->assertTrue($modelClassObject->methodExists('getName'), 'No method: getName');

	}

	/**
	 *
	 *
	 */
	public function classBuilderGeneratesIsMethodForBooleanProperty() {

		$domainObject = $this->buildDomainObject($this->modelName, true, true);

		$property = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty('blue');
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);

		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject, TRUE);
		$this->assertTrue($modelClassObject->methodExists('isBlue'), 'No method: isBlue');

	}

	/**
	 * @test
	 */
	public function classBuilderGeneratesMethodsForRelationProperty() {
		$modelName2 = 'Model2';
		$propertyName = 'relNames';

		$domainObject1 = $this->buildDomainObject($this->modelName, true, true);
		$relatedDomainObject = $this->buildDomainObject($modelName2);

		$relationProperty = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ManyToManyRelation($propertyName);
		$relationProperty->setForeignModel($relatedDomainObject);
		$domainObject1->addProperty($relationProperty);

		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject1, TRUE);

		$this->assertTrue($modelClassObject->methodExists('add' . ucfirst(Inflector::singularize($propertyName))), 'Add method was not generated');
		$this->assertTrue($modelClassObject->methodExists('remove' . ucfirst(Inflector::singularize($propertyName))), 'Remove method was not generated');
		$this->assertTrue($modelClassObject->methodExists('set' . ucfirst($propertyName)), 'Setter was not generated');
		$this->assertTrue($modelClassObject->methodExists('set' . ucfirst($propertyName)), 'Setter was not generated');

		$addMethod = $modelClassObject->getMethod('add' . ucfirst(Inflector::singularize($propertyName)));
		$this->assertTrue($addMethod->isTaggedWith('param'), 'No param tag set for setter method');
		$paramTagValues = $addMethod->getTagsValues('param');
		$this->assertTrue((strpos($paramTagValues, $relatedDomainObject->getFullQualifiedClassName()) === 0), 'Wrong param tag:' . $paramTagValues);

		$parameters = $addMethod->getParameters();
		$this->assertTrue((count($parameters) == 1), 'Wrong parameter count in add method');
		$parameter = current($parameters);
		$this->assertTrue(($parameter->getName() == Inflector::singularize($propertyName)), 'Wrong parameter name in add method');
		$this->assertTrue(($parameter->getTypeHint() == $relatedDomainObject->getFullQualifiedClassName()), 'Wrong type hint for add method parameter:' . $parameter->getTypeHint());

	}

}

?>
