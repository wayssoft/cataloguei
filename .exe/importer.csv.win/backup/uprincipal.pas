unit uPrincipal;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, ExtCtrls, ComCtrls,
  DBGrids, Buttons, StdCtrls, Grids, ActnList, ZConnection, ZDataset,
  // units
  uRequest4Pascal,
  //Formularios
  uLogin, uConex,
  uImportarCSV, DB;

type

  { TfrmPrincipal }

  TfrmPrincipal = class(TForm)
    act_atualizar_lista: TAction;
    ActionList1: TActionList;
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
    procedure act_atualizar_listaExecute(Sender: TObject);
    procedure FormCreate(Sender: TObject);
    procedure FormShow(Sender: TObject);
    procedure spBtImportarProdutosClick(Sender: TObject);
    procedure SpeedButton1Click(Sender: TObject);
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

procedure TfrmPrincipal.act_atualizar_listaExecute(Sender: TObject);
var
  i, // linha
  j: // coluna
  Integer;
begin
  // atualiza a tabela
  with DM.qry_produtos do begin
     Close;
     SQL.Clear;
     SQL.Add('SELECT * FROM produtos');
     Open;
     Last;
     First;
  end;

  // Definindo o número de linhas e colunas (opcional se já estiver definido visualmente)
  StringGrid1.RowCount := DM.qry_produtos.RecordCount + 1; // número de linhas
  StringGrid1.ColCount := 6; // número de colunas

  // Ajusta o cabecalho
  i:=0;
  j:=0;
  StringGrid1.Cells[0, i] := 'identificador';
  StringGrid1.Cells[1, i] := 'Codigo barras';
  StringGrid1.Cells[2, i] := 'Nome';
  StringGrid1.Cells[3, i] := 'Descrição';
  StringGrid1.Cells[4, i] := 'Preço';
  StringGrid1.Cells[5, i] := 'Estoque';

  // ajustar largura das colunas
  StringGrid1.ColWidths[0] := 110;
  StringGrid1.ColWidths[1] := 110;
  StringGrid1.ColWidths[2] := 230;
  StringGrid1.ColWidths[3] := 350;
  StringGrid1.ColWidths[0] := 80;
  StringGrid1.ColWidths[0] := 80;

  while not DM.qry_produtos.EOF do begin
    i:=i+1;
    StringGrid1.Cells[0, i] := DM.qry_produtosidentificador.AsString;
    StringGrid1.Cells[1, i] := DM.qry_produtoscodigo_barras.AsString;
    StringGrid1.Cells[2, i] := DM.qry_produtosnome.AsString;
    StringGrid1.Cells[3, i] := DM.qry_produtosdescricao.AsString;
    StringGrid1.Cells[4, i] := 'R$ '+FormatFloat('0.00',DM.qry_produtospreco.AsFloat);
    StringGrid1.Cells[5, i] := FloatToStr(DM.qry_produtosquantidade.AsFloat);
    DM.qry_produtos.Next;
  end;

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

procedure TfrmPrincipal.SpeedButton1Click(Sender: TObject);
begin
  act_atualizar_lista.Execute;
end;

end.

