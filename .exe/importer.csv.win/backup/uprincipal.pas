unit uPrincipal;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, ExtCtrls, ComCtrls,
  DBGrids, Buttons, StdCtrls, Grids, ZConnection, ZDataset,
  // units
  uRequest4Pascal,
  //Formularios
  uLogin, uConex,
  uImportarCSV, DB;

type

  { TfrmPrincipal }

  TfrmPrincipal = class(TForm)
    ds_produtos: TDataSource;
    Image1: TImage;
    Label1: TLabel;
    PageControl1: TPageControl;
    Panel4: TPanel;
    spBtImportarProdutos: TSpeedButton;
    SpeedButton1: TSpeedButton;
    StringGrid1: TStringGrid;
    TabSheet2: TTabSheet;
    TabSheet3: TTabSheet;
    procedure FormCreate(Sender: TObject);
    procedure FormShow(Sender: TObject);
    procedure spBtImportarProdutosClick(Sender: TObject);
  private

  public

  end;

var
  frmPrincipal: TfrmPrincipal;

implementation

{$R *.lfm}

{ TfrmPrincipal }

procedure TfrmPrincipal.FormCreate(Sender: TObject);
begin
  // cria a conexão com banco de dados
  DM.ZConnection1.Database:=ExtractFilePath(ParamStr(0))+'data.db';
  DM.ZConnection1.Connected:=True;
  If (frmLogin = Nil) Then // Verifica se o formulário está vazio (Nil).
  frmLogin := TfrmLogin.Create(Application); // Cria o formulário.
  frmLogin.WindowState := wsNormal; // Se o usuario maximizou ou minizou o formulário, ele volta para o tamanho nornal.
  frmLogin.ShowModal; //ou Form1.ShowModal - Mostra o formulário na tela.
end;

procedure TfrmPrincipal.FormShow(Sender: TObject);
begin
  with DM.qry_produtos do
  begin
     Close;
     SQL.Clear;
     SQL.Add('SELECT * FROM produtos');
     Open;
  end;
end;

procedure TfrmPrincipal.spBtImportarProdutosClick(Sender: TObject);
begin
  If (FrmImportarCsvProdutos = Nil) Then // Verifica se o formulário está vazio (Nil).
  FrmImportarCsvProdutos := TFrmImportarCsvProdutos.Create(Application); // Cria o formulário.
  FrmImportarCsvProdutos.WindowState := wsNormal; // Se o usuario maximizou ou minizou o formulário, ele volta para o tamanho nornal.
  FrmImportarCsvProdutos.ShowModal; //ou Form1.ShowModal - Mostra o formulário na tela.
end;

end.

