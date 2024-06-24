unit uLogin;

{$mode ObjFPC}{$H+}

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, ExtCtrls, StdCtrls,
  MaskEdit, ZDataset,
  // Uses para ultilizar o Request4Pascal
  uRequest4pascal,
  fpjson,
  jsonparser, uConex;

type

  { TfrmLogin }

  TfrmLogin = class(TForm)
    Button1: TButton;
    Button2: TButton;
    CheckBox1: TCheckBox;
    edtSenha: TEdit;
    Image1: TImage;
    Label1: TLabel;
    Label2: TLabel;
    mEdtNumero: TMaskEdit;
    procedure Button1Click(Sender: TObject);
    procedure Button2Click(Sender: TObject);
    procedure FormClose(Sender: TObject; var CloseAction: TCloseAction);
    procedure FormShow(Sender: TObject);
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

procedure TfrmLogin.FormShow(Sender: TObject);
begin
  with DM.qry_config do begin
      Close;
      SQL.Clear;
      SQL.Add('select * from config WHERE id = 1');
      Open;
  end;
  mEdtNumero.Clear;
  edtSenha.Clear;
end;

procedure TfrmLogin.Button2Click(Sender: TObject);
begin
  Application.Terminate;
  Close;
end;

procedure TfrmLogin.Button1Click(Sender: TObject);
var
  url: String;
  body: String;
  headers: TStrings;
  JSONResponse: TJSONObject;
  UserObject: TJSONObject;
  request: TRequest4pascal;
  TokenValue:String;
  logInSuccess:Boolean;
begin
  logInSuccess := False;
  url := 'https://wayssoft.com.br/api/v1/cataloguei/login.php';
  body := '{'
  +'"num_whatsapp":"'+mEdtNumero.Text+'",'
  +' "senha":"'+edtSenha.Text+'"}';
  headers := TStringList.Create;
  headers.Add('Authorization: Bearer your_token_here');

  request := TRequest4pascal.Create;
  try
    JSONResponse := request._post(url, body, headers);
    // Processar a resposta JSON
    if JSONResponse.IndexOfName('status') <> -1 then
    begin
      if JSONResponse.Get('status') = 'success' then begin
         UserObject := TJSONObject(JSONResponse.FindPath('user'));
         // Get the token value
         TokenValue := UserObject.Get('token', '');
         TokenValue := UserObject.Get('dominio', '');
         logInSuccess := True;
      end else begin
        ShowMessage(JSONResponse.Get('message'));
        logInSuccess := False;
      end;
    end;

  finally
    headers.Free;
    request.Free;
  end;
  If logInSuccess = True then Begin
    // salva o token na base de dados
    DM.qry_config.Edit;
    DM.qry_configtoken.AsString:=TokenValue;
    DM.qry_config.Post;
    Close;
  end;
end;

end.

