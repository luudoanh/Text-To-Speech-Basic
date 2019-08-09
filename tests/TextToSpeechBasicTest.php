<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\TextToSpeechBasic;

final class TestTextToSpeechBasic extends TestCase
{
	
	/**
     * Get a non public property of an object
     *
     * @param object $obj
     * @param string $property
     * @return mixed
     */
	protected function getNonPublicProperty($obj, $property)
	{
		if (!is_object($obj) || !is_string($property)) {
			return null;
		}
		$ref = new \ReflectionProperty(get_class($obj), $property);
		$ref->setAccessible(true);

		return $ref->getValue($obj);
	}

    /**
     * Set value for a non public property of an object
     *
     * @param object $obj
     * @param string $property
     * @param mixed  $value
     */
    protected function setNonPublicProperty($obj, $property, $value)
    {
    	if (!is_object($obj) || !is_string($property)) {
    		return;
    	}
    	$ref = new \ReflectionProperty(get_class($obj), $property);
    	$ref->setAccessible(true);

    	$ref->setValue($obj, $value);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $obj    Instantiated object that we will run method on.
     * @param string $method Method name to call
     * @param array  $params Array of parameters to pass into method.
     * @return mixed         Method return.
     * @throws \InvalidArgumentException
     */
    protected function invokeNonPublicMethod($obj, $method, $params = [])
    {
    	if (!is_object($obj) || !is_string($method)) {
    		throw new \InvalidArgumentException();
    	}
    	$ref = new \ReflectionMethod($obj, $method);
    	$ref->setAccessible(true);

    	return $ref->invokeArgs($obj, $params);
    }

	/**
	 * [testGetFromFileByPathGroupToArray] Function to test the return value of function fromFile
	 * @param  [array] $desiredArrayReceived [desired array received]
	 * @param  [string] $filePath             [file path]
	 * @dataProvider providerTestGetFromFilePathGroupToArray
	 */
	public function testGetFromFileByPathGroupToArray($desiredArrayReceived, $filePath)
    {
      $tts = new TextToSpeechBasic();
      $result = $this->invokeNonPublicMethod($tts, 'fromFile', [$filePath]);
      $resultFinal = $this->arrayAreSmilar($desiredArrayReceived, $result);
      $this->assertTrue($resultFinal);
  }

  public function providerTestGetFromFilePathGroupToArray()
  {
      $rawArray = Array('Chương 1: Có lẽ gặp nhau đã là một sai lầm?Nhiều năm rồi, mà bầu trời hôm nay vẫn trong xanh như vậy. Nắng xuống đẩy bầu trời lên cao hơn',
        ' Những vệt nắng cuối ngày kéo dài trên mặt đường và dường như bóng tôi cũng kéo dài theo đến vô cùng vô tận. Hoàng hôn buông mình xuống',
        ' phủ đều lên những hàng liễu mảnh mai đang rũ mình khuấy động những con sóng lăn tăn trên mặt hồ',
        ' không rõ là do cảnh chiều tà khiến lòng đột nhiên có chút nhốn nháo, hay là trong tôi vốn đã mang tâm trạng của những ngày cũ',
        'Tôi lặng im dạo bước trên đường Hồ tây Hà Nội, cố thả lỏng mình thư thái để tận hưởng những dư âm cuối ngày',
        ' Buông mình trong một không gian trong lành, tôi hít một hơi thật sâu rồi dừng chân lắng nghe âm thanh của cuộc sống văng vẳng vọng lại lúc xa lúc gần',
        ' Tôi nắm lấy thành lan can mát lạnh, thả mình theo làn gió nhẹ nhàng của ngày đông.Tôi nhìn về phía trước, không biết là nhìn gì',
        ' chỉ biết tôi đang hướng đến một chân trời xa xăm, hay chính là nhìn về những kí ức cũ.Tôi buông mắt xuống hồi tưởng đầy nhớ nhung. Gió thổi nhẹ',
        ' những ngày xưa ấy như một thước phim chầm chậm quay trở về.');
      $rawPath = 'tests/TextToTest/text.txt';
      return [
         [$rawArray, $rawPath]
     ];
 }

 private function arrayAreSmilar($rawArray, $arrayHasBeenProcessed)
 {
  if (count(array_diff_assoc($rawArray, $arrayHasBeenProcessed))) {
     return false;
 } else {
     return true;
 }
}
}