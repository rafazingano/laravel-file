<?php

namespace ConfrariaWeb\File\Services;

use ConfrariaWeb\File\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileService
{

    protected $model;
    protected $description;
    protected $status;
    protected $type;
    protected $size;
    protected $mimeType;

    /**
     * Define o modelo e outras informações para o arquivo a ser armazenado.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $model O modelo ao qual o arquivo será vinculado (opcional)
     * @param string|null $description Uma descrição para o arquivo (opcional)
     * @param bool $status O status do arquivo (opcional)
     * @return $this
     */
    public function model($model = null, $description = null, $status = true)
    {
        $this->model = $model;
        $this->description = $description;
        $this->status = $status;
        return $this;
    }

    /**
     * Salva o arquivo no storage e, se fornecido um modelo, armazena as informações no banco.
     *
     * @param mixed $fileData Pode ser um \Illuminate\Http\Request, uma URL ou o caminho local da imagem (pode ser um array)
     * @param string $storagePath O caminho para armazenar o arquivo no storage
     * @param string|null $inputName O nome do input do arquivo no request (padrão: 'file')
     * @return array|string|null O caminho completo do arquivo armazenado ou null em caso de falha
     */
    public function storeFile($fileData, $storagePath, $inputName = 'file')
    {
        if (is_array($fileData)) {
            // Se for um array, chamamos o método recursivamente para cada item
            $filePaths = [];
            foreach ($fileData as $fileItem) {
                $filePaths[] = $this->storeFile($fileItem, $storagePath, $inputName);
            }
            return $filePaths;
        } elseif ($fileData instanceof Request) {
            // Se for um Request, pegamos o arquivo através do input
            //$file = $fileData->file($inputName);

            // Se for um Request, pegamos o(s) arquivo(s) através do input
            $files = $fileData->file($inputName);
            // Verifica se foi retornado um arquivo ou um array de arquivos válidos
            if (is_array($files)) {
                $filePaths = [];
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $filePaths[] = $this->storeUploadedFile($file, $storagePath);
                    } else {
                        throw new FileException('Arquivo inválido no input do request.');
                    }
                }
                return $filePaths;
            } elseif ($files instanceof UploadedFile) {
                return $this->storeUploadedFile($files, $storagePath);
            } else {
                throw new FileException('Arquivo inválido no input do request.');
            }

            //dd($file);
            //return $this->storeUploadedFile($file, $storagePath);

            return $filePaths;
        } elseif ($fileData instanceof UploadedFile) {
            return $this->storeUploadedFile($fileData, $storagePath);
        } elseif (is_string($fileData)) {
            // Se for uma URL válida, fazemos o download do arquivo
            if ($this->isValidUrl($fileData)) {
                $file = $this->downloadFileFromUrl($fileData);
                return $this->storeDownloadedFile($file, $storagePath);
            } elseif (file_exists($fileData)) {
                // Se for um caminho local válido, usamos o arquivo diretamente
                $file = new UploadedFile($fileData, basename($fileData));
                return $this->storeUploadedFile($file, $storagePath);
            }
        }

        throw new FileException('Arquivo inválido ou caminho não encontrado.');
    }

    /**
     * Salva um arquivo enviado via upload no storage e, se fornecido um modelo, armazena as informações no banco.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $storagePath O caminho para armazenar o arquivo no storage
     * @return string O caminho completo do arquivo armazenado
     */
    protected function storeUploadedFile(UploadedFile $file, $storagePath)
    {
        // Gera um nome único para o arquivo
        $fileName = $file->hashName();

        // Salva o arquivo no storage
        $filePath = $file->storeAs($storagePath, $fileName);

        $this->storeFileModel($file, $filePath);

        return $filePath;
    }

    protected function storeFileModel($file, $filePath)
    {
        // Se um modelo foi fornecido, armazena as informações no banco
        if ($this->model instanceof \Illuminate\Database\Eloquent\Model) {
            $fileModel = new File([
                'path' => $filePath,
                'name' => $file->getClientOriginalName(),
                'description' => $this->description,
                'status' => $this->status,
                'type' => $file->getClientOriginalExtension(), // Define o tipo do arquivo como a extensão original
                'size' => $file->getSize(), // Define o tamanho do arquivo em bytes
                'mime_type' => $file->getMimeType(), // Define o MIME type do arquivo
            ]);

            $this->model->files()->save($fileModel);
        }

        // Limpa os valores após salvar o arquivo
        //$this->resetModelData();
    }

    /**
     * Faz o download de um arquivo a partir de uma URL.
     *
     * @param string $url
     * @return \Illuminate\Http\UploadedFile
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    protected function downloadFileFromUrl($url)
    {
        $contents = file_get_contents($url);

        if ($contents === false) {
            throw new FileException('Falha ao fazer o download do arquivo da URL fornecida.');
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'FileService');
        file_put_contents($tempFile, $contents);

        $file = new UploadedFile($tempFile, basename($url));
        return $file;
    }

    /**
     * Salva um arquivo previamente baixado (downloaded) no storage.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $storagePath O caminho para armazenar o arquivo no storage
     * @return string O caminho completo do arquivo armazenado
     */
    protected function storeDownloadedFile(UploadedFile $file, $storagePath)
    {
        return $this->storeUploadedFile($file, $storagePath);
    }

    /**
     * Verifica se a string é uma URL válida.
     *
     * @param string $url
     * @return bool
     */
    protected function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Reseta os valores do modelo e outras informações para evitar interferência entre as chamadas.
     */
    protected function resetModelData()
    {
        $this->model = null;
        $this->description = null;
        $this->status = true;
    }
}
