unit uRequest4Pascal;

{$mode ObjFPC}{$H+}

interface

uses
  Classes, SysUtils,Dialogs,
  // converter imagem
  base64,
  FPImage,
  FPCanvas,
  FPImgCanv,
  FPReadJPEG,
  FPWriteJPEG,
  FPReadPNG,
  FPWritePNG,
  // request
  fphttpclient,
  opensslsockets,
  fpjson,
  jsonparser,
  RegExpr;

type

  { TRequest4pascal }

  TRequest4pascal = Class
    private

    public
      function _get(url: String; const headers: TStrings = nil): TJSONObject;
      function _post(url: String; body: String; const headers: TStrings = nil): TJSONObject;
      function ImageToBase64(const ImagePath: string): string;
  end;

implementation

{ TRequest4pascal }

function TRequest4pascal._get(url: String; const headers: TStrings = nil): TJSONObject;
var
  HTTPClient: TFPHTTPClient;
  JSONResponse: TJSONObject;
  respostaHTTP: TStringStream;
  i: Integer;
begin
  try
    HTTPClient := TFPHttpClient.Create(nil);
    HTTPClient.AddHeader('Content-Type', 'application/json');

    // Adiciona os cabeçalhos fornecidos, se houver
    if headers <> nil then
    begin
      for i := 0 to headers.Count - 1 do
        HTTPClient.AddHeader(headers.Names[i], headers.ValueFromIndex[i]);
    end;

    respostaHTTP := TStringStream.Create;
    respostaHTTP.WriteString(HTTPClient.Get(url));

    // Processa a resposta JSON
    JSONResponse := TJSONObject(GetJSON(respostaHTTP.DataString));
    Result := JSONResponse;
  finally
    respostaHTTP.Free;
    HTTPClient.Free;
  end;
end;

function TRequest4pascal._post(url: String; body: String; const headers: TStrings = nil
): TJSONObject;
var
  HTTPClient: TFPHTTPClient;
  JSONResponse: TJSONObject;
  respostaHTTP: TStringStream;
  RequestBody: TStringStream;
  i: Integer;
begin
  try
    HTTPClient := TFPHttpClient.Create(nil);
    HTTPClient.AddHeader('Content-Type', 'application/json');

    // Adiciona os cabeçalhos fornecidos, se houver
    if headers <> nil then
    begin
      for i := 0 to headers.Count - 1 do
        HTTPClient.AddHeader(headers.Names[i], headers.ValueFromIndex[i]);
    end;

    // Cria o corpo do pedido como uma TStringStream
    RequestBody := TStringStream.Create(body, TEncoding.UTF8);
    respostaHTTP := TStringStream.Create;

    // Envia o pedido POST
    HTTPClient.RequestBody := RequestBody;
    HTTPClient.Post(url, respostaHTTP);

    // Processa a resposta JSON
    ShowMessage(respostaHTTP.DataString);
    JSONResponse := TJSONObject(GetJSON(respostaHTTP.DataString));
    Result := JSONResponse;
  finally
    // Libera os recursos
    respostaHTTP.Free;
    RequestBody.Free;
    HTTPClient.Free;
  end;
end;

function TRequest4pascal.ImageToBase64(const ImagePath: string): string;
var
  Image: TFPMemoryImage;
  Reader: TFPCustomImageReader;
  Writer: TFPCustomImageWriter;
  ImageStream: TMemoryStream;
  Base64Stream: TStringStream;
  Base64Encoder: TBase64EncodingStream;
begin
  Image := TFPMemoryImage.Create(0, 0);
  ImageStream := TMemoryStream.Create;
  Base64Stream := TStringStream.Create('');
  try
    // Seleciona o leitor baseado na extensão do arquivo
    case LowerCase(ExtractFileExt(ImagePath)) of
      '.jpg', '.jpeg': Reader := TFPReaderJPEG.Create;
      '.png': Reader := TFPReaderPNG.Create;
    else
      raise Exception.Create('Unsupported image format');
    end;

    // Seleciona o escritor baseado na extensão do arquivo
    case LowerCase(ExtractFileExt(ImagePath)) of
      '.jpg', '.jpeg': Writer := TFPWriterJPEG.Create;
      '.png': Writer := TFPWriterPNG.Create;
    else
      raise Exception.Create('Unsupported image format');
    end;

    // Carrega a imagem do arquivo
    Image.LoadFromFile(ImagePath, Reader);

    // Salva a imagem em um stream de memória
    Image.SaveToStream(ImageStream, Writer);
    ImageStream.Position := 0;

    // Converte o stream da imagem para base64
    Base64Encoder := TBase64EncodingStream.Create(Base64Stream);
    Base64Encoder.CopyFrom(ImageStream, ImageStream.Size);
    Base64Encoder.Flush;

    Result := Base64Stream.DataString;
  finally
    Image.Free;
    ImageStream.Free;
    Base64Stream.Free;
    Reader.Free;
    Writer.Free;
    Base64Encoder.Free;
  end;
end;

end.

