<? function uploadToImageKit($filePath, $fileName) {
  $privateApiKey = 'private_ROkEtEXNY4WW4roFa0LBBdXfHXw=';
  $folder = '/uploads';
  $url = 'https://upload.imagekit.io/api/v1/files/upload';

  $fileData = curl_file_create($filePath, mime_content_type($filePath), $fileName);

  $postData = [
    'file' => $fileData,
    'fileName' => $fileName,
    'folder' => $folder,
    'useUniqueFileName' => 'true'
  ];

  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD => $privateApiKey . ':',
    CURLOPT_POSTFIELDS => $postData
  ]);

  $response = curl_exec($ch);
  curl_close($ch);

  $result = json_decode($response, true);
  return $result['url'] ?? null;
}


