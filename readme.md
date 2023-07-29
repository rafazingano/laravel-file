# Laravel File Package - ConfrariaWeb\File

O Laravel File Package é um pacote para Laravel que fornece um serviço de gerenciamento de arquivos para facilitar o upload e armazenamento de arquivos no storage do Laravel. Com este pacote, você pode realizar o upload de arquivos por meio de um Request, fornecer uma URL para download do arquivo ou até mesmo passar o caminho local de um arquivo para ser armazenado.

O gerenciamento de arquivos é uma tarefa comum em muitos projetos web, e o Laravel fornece recursos nativos para lidar com o armazenamento de arquivos usando o sistema de arquivos local ou serviços de armazenamento em nuvem, como Amazon S3. No entanto, pode ser um pouco trabalhoso implementar o tratamento de múltiplas modalidades de upload (Request, URL e caminho local) e fazer a diferenciação de cada uma delas.

O pacote Laravel File simplifica esse processo, fornecendo uma classe de serviço chamada FileService, que é capaz de lidar com todas essas modalidades de forma transparente e intuitiva. Com este pacote, você pode fazer uploads de arquivos usando qualquer uma das opções mencionadas e ainda realizar o upload de vários arquivos de uma só vez.

## Instalação
Para instalar o pacote em seu projeto Laravel, você pode utilizar o Composer:
```bash
composer require confrariaweb/laravel-file
```

## Uso
Após a instalação do pacote, você pode começar a utilizá-lo em seu projeto. Para isso, siga os passos abaixo:

 1. Importe a classe FileService onde desejar utilizá-la:

```php
use ConfrariaWeb\File\FileService;
```

2. Instancie a classe FileService:

```php
$fileService = new FileService();
```

3. Use o método storeFile() para fazer o upload do arquivo:

```php
// Exemplo com Request
$fileService->storeFile($request, 'uploads');
// Exemplo com URL
$fileService->storeFile('https://example.com/image.jpg', 'uploads');
// Exemplo com caminho local
$fileService->storeFile('/path/to/local/image.jpg', 'uploads');
```

## Exemplos de Uploads

A seguir, veja alguns exemplos práticos de como fazer uploads usando o Laravel File Package.

Upload de arquivo único usando Request

```php
use ConfrariaWeb\File\FileService;
use Illuminate\Http\Request;

class FileController extends Controller
{
	public function upload(Request $request)
	{
		$fileService = new FileService();
		$filePath = $fileService->storeFile($request, 'uploads');
		// Faça algo com o caminho do arquivo armazenado, se necessário
		return response()->json(['message' => 'Arquivo enviado com sucesso.', 'file_path' => $filePath]);
	}
}
```

Upload de vários arquivos usando URLs

```php
use ConfrariaWeb\File\FileService;

class FileController extends Controller
{
	public function uploadFromUrls()
	{
		$fileService = new FileService();
		$filePaths = $fileService->storeFile([
			'https://example.com/image1.jpg',
			'https://example.com/image2.jpg',
			'https://example.com/image3.jpg',
		], 'uploads');
		// Faça algo com os caminhos dos arquivos armazenados, se necessário
		return response()->json(['message' => 'Arquivos enviados com sucesso.', 'file_paths' => $filePaths]);
	}
}
```

Upload de um único arquivo usando caminho local

```php
use ConfrariaWeb\File\FileService;

class FileController extends Controller
{
	public function uploadFromLocal()
	{
		$fileService = new FileService();
		$filePath = $fileService->storeFile('/path/to/local/image.jpg', 'uploads');
		// Faça algo com o caminho do arquivo armazenado, se necessário
		return response()->json(['message' => 'Arquivo enviado com sucesso.', 'file_path' => $filePath]);
	}
}
```

Upload de vários arquivos usando caminhos locais

```php
use ConfrariaWeb\File\FileService;

class FileController extends Controller
{
	public function uploadMultipleFromLocal()
	{
		$fileService = new FileService();
		$filePaths = $fileService->storeFile([
			'/path/to/local/image1.jpg',
			'/path/to/local/image2.jpg',
			'/path/to/local/image3.jpg',
		], 'uploads');
		// Faça algo com os caminhos dos arquivos armazenados, se necessário
		return response()->json(['message' => 'Arquivos enviados com sucesso.', 'file_paths' => 	$filePaths]);
	}
}
```

## Como usar a vinculação opcional de modelos

Para utilizar a vinculação opcional de modelos ao fazer o upload de um arquivo, você pode seguir o seguinte padrão de uso:

```php
use ConfrariaWeb\File\FileService;
use App\Models\User; // Substitua pelo modelo que deseja vincular (se aplicável)

// ...

// Crie uma instância do FileService
$fileService = new FileService();

// Defina o modelo, descrição e status do arquivo (opcional)
$model = User::find(1); // Substitua pelo modelo que deseja vincular
$description = 'Descrição do arquivo';
$status = true;

// Chame o método model() para definir as informações do modelo
$fileService->model($model, $description, $status);

// Use o método storeFile() para fazer o upload do arquivo e, se fornecido um modelo, salvá-lo no banco de dados
$filePath = $fileService->storeFile('/path/to/local/file.txt', 'uploads');

// ...
```

No exemplo acima, o arquivo /path/to/local/file.txt será armazenado no storage do Laravel e, se o modelo User::find(1) for fornecido, as informações relacionadas ao arquivo, como type, size, mime_type, description e status, serão armazenadas na tabela files do banco de dados.

## Exemplos práticos
A seguir, veja alguns exemplos práticos de como fazer o upload de arquivos com e sem vinculação a modelos:

1. Upload de arquivo sem vinculação a um modelo
```php
use ConfrariaWeb\File\FileService;

// ...

$fileService = new FileService();
$filePath = $fileService->storeFile('/path/to/local/image.jpg', 'uploads');

// O arquivo 'image.jpg' será armazenado no storage, sem vinculação a nenhum modelo.
```

2. Upload de arquivo vinculado a um modelo
```php
use ConfrariaWeb\File\FileService;
use App\Models\Post;

// ...

$fileService = new FileService();

// Defina o modelo, descrição e status do arquivo (opcional)
$model = Post::find(1); // Substitua pelo modelo que deseja vincular
$description = 'Imagem de capa do post';
$status = true;

// Chame o método model() para definir as informações do modelo
$fileService->model($model, $description, $status);

// Use o método storeFile() para fazer o upload do arquivo e vinculá-lo ao modelo 'Post::find(1)' no banco de dados
$filePath = $fileService->storeFile('/path/to/local/cover.jpg', 'uploads');

// O arquivo 'cover.jpg' será armazenado no storage e as informações relacionadas serão salvas na tabela 'files', vinculadas ao modelo 'Post::find(1)'.
```

Com a funcionalidade de vinculação opcional de modelos, o pacote Laravel File oferece mais flexibilidade para armazenar informações adicionais dos arquivos, tornando-o ainda mais versátil em suas aplicações. Experimente e adapte essa funcionalidade de acordo com suas necessidades específicas!

## Contribuições

Se você encontrar algum problema ou tiver sugestões de melhorias, sinta-se à vontade para criar uma issue ou enviar um pull request no repositório do pacote: Laravel File Package.

## Licença

Este pacote é licenciado sob a Licença MIT.

## Autor

#### O Laravel File Package foi desenvolvido por Rafael Zingano.

Com o Laravel File Package, você pode simplificar o processo de gerenciamento de arquivos em seu projeto Laravel, permitindo uploads fáceis e eficientes, independentemente da origem dos arquivos. O pacote foi criado para atender às necessidades comuns de upload e armazenamento de arquivos, oferecendo flexibilidade e facilidade de uso. Sinta-se à vontade para utilizar o pacote em seus projetos e, se possível, contribuir com sugestões e melhorias para torná-lo ainda melhor!