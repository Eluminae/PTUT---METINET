<?php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use AppBundle\Models\Campaign;
use AppBundle\Models\File;
use AppBundle\Dtos\RealisationRegistration;

use Symfony\Component\Config\Definition\Exception\Exception;

class FileFactory
{
	public function createRealisationFile(RealisationRegistration $realisationRegistration, $campaignId)
	{
		$realisationId = uniqid();
		$realisationFile =  $realisationRegistration->file;

		$extension = $realisationFile->guessExtension();
		if (!$extension) {
			// extension cannot be guessed
			throw new Exception('PAS D\'EXTENSION VALIDE');
		}

		$fileName = $campaignId.'-'.$realisationId.'.'.$extension;
		$dir = '';

		//$this->registerFile($realisationFile, $dir, $fileName);

		return new File(
			uniqid(), 
			$fileName,
			$extension// A changer pour avoir le format avec objet de format de fichier
			);
	}

	public function registerFile($file, $dir, $name)
	{
		$file->move($dir, $name);
	}
}