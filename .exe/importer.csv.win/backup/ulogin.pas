unit uLogin;

{$mode ObjFPC}{$H+}

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, ExtCtrls, StdCtrls,
  MaskEdit, ZDataset,
  // Uses para ultilizar o Request4Pascal
  uRequest4pascal,
  fpjson,
  jsonparser;

type

  { TfrmLogin }

  TfrmLogin = class(TForm)
    Button1: TButton;
    Button2: TButton;
    CheckBox1: TCheckBox;
    Edit2: TEdit;
    Image1: TImage;
    Label1: TLabel;
    Label2: TLabel;
    MaskEdit1: TMaskEdit;
    qry_config: TZQuery;
    procedure Button1Click(Sender: TObject);
    procedure Button2Click(Sender: TObject);
    procedure FormClose(Sender: TObject; var CloseAction: TCloseAction);
  private

  public

  end;

var
  frmLogin: TfrmLogin;

implementation

{$R *.lfm}

{ TfrmLogin }

procedure TfrmLogin.FormClose(Sender: TObject; var CloseAction: TCloseAction);
begin
  frmLogin := Nil; // Deixa o formulário vazio
end;

procedure TfrmLogin.Button2Click(Sender: TObject);
begin
  //Application.Terminate;
  Close;
end;

procedure TfrmLogin.Button1Click(Sender: TObject);
var
  url: String;
  body: String;
  headers: TStrings;
  response: TJSONObject;
  request: TRequest4pascal;
begin
  url := 'https://wayssoft.com.br/api/v1/utils/check_license.php';
  body := '{"key1":"value1", "key2":"value2"}';
  headers := TStringList.Create;
  headers.Add('Authorization: Bearer your_token_here');

  request := TRequest4pascal.Create;
  try
    response := request._post(url, body, headers);
    // Processar a resposta JSON
  finally
    headers.Free;
    request.Free;
  end;
  Close;
end;

end.

