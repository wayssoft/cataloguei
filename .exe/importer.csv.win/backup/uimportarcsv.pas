unit uImportarCSV;

{$mode ObjFPC}{$H+}

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, StdCtrls, ExtCtrls,
  ComCtrls, Buttons,CsvDocument, uConex, ShellApi, LCLIntf;

type

  { TFrmImportarCsvProdutos }

  TFrmImportarCsvProdutos = class(TForm)
    btSelecionar: TButton;
    btImportar: TButton;
    Button3: TButton;
    cBoxCodigoBarras: TComboBox;
    cBoxImg: TComboBox;
    cBoxIdentificador: TComboBox;
    cBoxNomeProduto: TComboBox;
    cBoxDescricao: TComboBox;
    cBoxPreco: TComboBox;
    cBoxEstoque: TComboBox;
    Image1: TImage;
    Label1: TLabel;
    Label10: TLabel;
    Label11: TLabel;
    Label12: TLabel;
    Label13: TLabel;
    Label14: TLabel;
    Label15: TLabel;
    Label16: TLabel;
    Label17: TLabel;
    Label18: TLabel;
    Label19: TLabel;
    Label2: TLabel;
    Label3: TLabel;
    Label4: TLabel;
    Label5: TLabel;
    Label6: TLabel;
    Label7: TLabel;
    Label8: TLabel;
    Label9: TLabel;
    OpenDialog1: TOpenDialog;
    Panel1: TPanel;
    Panel2: TPanel;
    Panel3: TPanel;
    ProgressBar1: TProgressBar;
    SpeedButton1: TSpeedButton;
    SpeedButton2: TSpeedButton;
    procedure btSelecionarClick(Sender: TObject);
    procedure btImportarClick(Sender: TObject);
    procedure Button3Click(Sender: TObject);
    procedure FormClose(Sender: TObject; var CloseAction: TCloseAction);
    procedure FormShow(Sender: TObject);
    procedure SpeedButton1Click(Sender: TObject);
    procedure SpeedButton2Click(Sender: TObject);
  private

  public
    CSV: TCSVDocument;
    procedure ImportCSV(const FileName: string);
    procedure ImportCSVv2(const FileName: string);
    procedure DisplayColumnData(const FileName, ColumnTitle: string);
    procedure DisplayColumnDatav2(const ColumnTitle, campo: string);
    var
    ColumnIndexIND:Integer;
  end;

var
  FrmImportarCsvProdutos: TFrmImportarCsvProdutos;

implementation

{$R *.lfm}

{ TFrmImportarCsvProdutos }


function TrocaVirgPPto(Valor: string): String;
var i:integer;
begin
  if Valor <> ' ' then
   begin
    for i := 0 to Length(Valor) do
   begin
    if Valor[i]='.' then
   begin
    Valor[i]:=',';
end
 else if Valor[i] = ',' then
begin
       Valor[i]:='.';
    end;
end;
end;
Result := valor;
end;

procedure TFrmImportarCsvProdutos.btSelecionarClick(Sender: TObject);
begin
  OpenDialog1.Filter := 'CSV Files|*.csv';
  if OpenDialog1.Execute then
  begin
    ImportCSVv2(OpenDialog1.FileName);
    btImportar.Enabled:=True;
  end;
end;

procedure TFrmImportarCsvProdutos.btImportarClick(Sender: TObject);
begin
  btSelecionar.Enabled:=False;
  // identificador
  if cBoxIdentificador.ItemIndex <> -1 then
  begin
    DisplayColumnDatav2(cBoxIdentificador.Text, 'IND');
  end else begin ShowMessage('Não foi mapeado o identificador'); Exit; end;

  // codigo de barras
  if cBoxCodigoBarras.ItemIndex <> -1 then
  begin
    DisplayColumnDatav2(cBoxCodigoBarras.Text, 'GTIN');
  end else begin ShowMessage('Não foi mapeado o codigo de barras'); Exit; end;

  // nome do produto
  if cBoxNomeProduto.ItemIndex <> -1 then
  begin
    DisplayColumnDatav2(cBoxNomeProduto.Text, 'NOME');
  end else begin ShowMessage('Não foi mapeado o nome do produto'); Exit; end;

  // descrição do produto
  if cBoxDescricao.ItemIndex <> -1 then
  begin
    DisplayColumnDatav2(cBoxDescricao.Text, 'DESC');
  end else begin ShowMessage('Não foi mapeado a descrição do produto'); Exit; end;

  // preço do produto
  if cBoxPreco.ItemIndex <> -1 then
  begin
    DisplayColumnDatav2(cBoxPreco.Text, 'PRECO');
  end else begin ShowMessage('Não foi mapeado o preço do produto'); Exit; end;

  // estoque do produto
  if cBoxEstoque.ItemIndex <> -1 then
  begin
    DisplayColumnDatav2(cBoxEstoque.Text, 'ESTOQ');
  end else begin ShowMessage('Não foi mapeado o estoque do produto'); Exit; end;


  // caminho da imagem
  if cBoxImg.ItemIndex <> -1 then
  begin
    DisplayColumnDatav2(cBoxImg.Text, 'IMG');
  end else begin ShowMessage('Não foi mapeado o caminho da imagem do produto'); Exit; end;

  ShowMessage('Importação da tabela CSV realizada com sucesso');
  Close;

end;

procedure TFrmImportarCsvProdutos.Button3Click(Sender: TObject);
begin
  Close;
end;

procedure TFrmImportarCsvProdutos.FormClose(Sender: TObject;
  var CloseAction: TCloseAction);
begin
  FrmImportarCsvProdutos := Nil; // Deixa o formulário vazio
end;

procedure TFrmImportarCsvProdutos.FormShow(Sender: TObject);
begin
  btImportar.Enabled:=False;
  btSelecionar.Enabled:=True;
end;

procedure TFrmImportarCsvProdutos.SpeedButton1Click(Sender: TObject);
begin
  //ShellExecute(Handle,'open','https://wayssoft.tomticket.com/kb/cataloguei-desktop/identificador-do-produto',nil,nil);
  OpenURL('https://wayssoft.tomticket.com/kb/cataloguei-desktop/identificador-do-produto');
end;

procedure TFrmImportarCsvProdutos.SpeedButton2Click(Sender: TObject);
begin
  OpenURL('https://wayssoft.tomticket.com/kb/cataloguei-desktop/caminho-da-imagem-do-produto-na-tabela-csv-para-importacao-de-dados');
end;

procedure TFrmImportarCsvProdutos.ImportCSV(const FileName: string);
var
  CSVFile: TextFile;
  Line: string;
  Columns: TStringList;
begin
  if not FileExists(FileName) then Exit;

  AssignFile(CSVFile, FileName);
  Reset(CSVFile);

  try
    // Ler a primeira linha do arquivo CSV (cabeçalho)
    ReadLn(CSVFile, Line);
    Columns := TStringList.Create;
    try
      Columns.Delimiter := ';';
      Columns.DelimitedText := Line;

      // Limpar o ComboBox e adicionar os títulos das colunas
      cBoxCodigoBarras.Items.Clear;
      cBoxCodigoBarras.Items.AddStrings(Columns);

      cBoxNomeProduto.Items.Clear;
      cBoxNomeProduto.Items.AddStrings(Columns);

      cBoxDescricao.Items.Clear;
      cBoxDescricao.Items.AddStrings(Columns);

      cBoxPreco.Items.Clear;
      cBoxPreco.Items.AddStrings(Columns);

      cBoxEstoque.Items.Clear;
      cBoxEstoque.Items.AddStrings(Columns);

      cBoxImg.Items.Clear;
      cBoxImg.Items.AddStrings(Columns);

      cBoxIdentificador.Items.Clear;
      cBoxIdentificador.Items.AddStrings(Columns);
    finally
      Columns.Free;
    end;

  finally
    CloseFile(CSVFile);
  end;
end;

procedure TFrmImportarCsvProdutos.ImportCSVv2(const FileName: string);
var
  i: Integer;
begin
  if not FileExists(FileName) then Exit;

  CSV := TCSVDocument.Create;
  try
    CSV.LoadFromFile(FileName);

    //Repassa o total de linhas do arquivo csv
    // multiplica as linhas por 7
    ProgressBar1.Max:=(CSV.RowCount*7)-7;
    ProgressBar1.Position:=0;

    // Limpar o ComboBox e adicionar os títulos das colunas
    cBoxIdentificador.Items.Clear;
    cBoxCodigoBarras.Items.Clear;
    cBoxNomeProduto.Items.Clear;
    cBoxDescricao.Items.Clear;
    cBoxEstoque.Items.Clear;
    cBoxPreco.Items.Clear;
    cBoxImg.Items.Clear;
    for i := 0 to CSV.ColCount[0] - 1 do
    begin
      cBoxIdentificador.Items.Add(CSV.Cells[i, 0]);
      cBoxCodigoBarras.Items.Add(CSV.Cells[i, 0]);
      cBoxNomeProduto.Items.Add(CSV.Cells[i, 0]);
      cBoxDescricao.Items.Add(CSV.Cells[i, 0]);
      cBoxEstoque.Items.Add(CSV.Cells[i, 0]);
      cBoxPreco.Items.Add(CSV.Cells[i, 0]);
      cBoxImg.Items.Add(CSV.Cells[i, 0]);
    end;
  except
    on E: Exception do
      ShowMessage('Erro ao carregar o arquivo CSV: ' + E.Message);
  end;
end;

procedure TFrmImportarCsvProdutos.DisplayColumnData(const FileName,
  ColumnTitle: string);
var
  CSVFile: TextFile;
  Line, CellData: string;
  Columns, RowData: TStringList;
  ColumnIndex: Integer;
begin
  if not FileExists(FileName) then Exit;

  AssignFile(CSVFile, FileName);
  Reset(CSVFile);

  try
    // Ler a primeira linha do arquivo CSV (cabeçalho)
    ReadLn(CSVFile, Line);
    Columns := TStringList.Create;
    RowData := TStringList.Create;
    try
      Columns.Delimiter := ';';
      Columns.DelimitedText := Line;

      // Obter o índice da coluna selecionada
      ColumnIndex := Columns.IndexOf(ColumnTitle);
      if ColumnIndex = -1 then Exit;

      // Limpar o Memo
      //Memo1.Clear;

      // Ler e exibir os dados da coluna selecionada
      while not Eof(CSVFile) do
      begin
        ReadLn(CSVFile, Line);
        RowData.Delimiter := ';';
        RowData.DelimitedText := Line;

        if ColumnIndex < RowData.Count then
        begin
          CellData := RowData[ColumnIndex];
          //Memo1.Lines.Add(CellData);
        end;
      end;
    finally
      Columns.Free;
      RowData.Free;
    end;
  finally
    CloseFile(CSVFile);
  end;
end;

procedure TFrmImportarCsvProdutos.DisplayColumnDatav2(const ColumnTitle,
  campo: string);
var
  ColumnIndex, i: Integer;
begin
  if not Assigned(CSV) then Exit;

  // Obter o índice da coluna selecionada
  ColumnIndex := -1;
  for i := 0 to CSV.ColCount[1] - 1 do
  begin
    if CSV.Cells[i, 0] = ColumnTitle then
    begin
      ColumnIndex := i;
      Break;
    end;
  end;

  if ColumnIndex = -1 then Exit;

  // Ler e exibir os dados da coluna selecionada
  for i := 1 to CSV.RowCount - 1 do
  begin

    // verifica qual campo e para fazer o insert
    //  se o campo for IND = identificador faz um insert na tabela


    if campo = 'IND' then begin
      // primeiro verifica se esse identificador ja foi informado
      ColumnIndexIND := ColumnIndex;
      with DM.qry_produtos do
      begin
        Close;
        SQL.Clear;
        SQL.Add('SELECT * FROM produtos WHERE identificador = '+QuotedStr(CSV.Cells[ColumnIndex, i]));
        Open;
      end;
      if DM.qry_produtos.RecordCount > 0 then begin
        // salva um log
        DM.qry_produtos.Edit;
        DM.qry_produtosstatus.AsString:='pending';
        DM.qry_produtos.Post;
      end else begin
        DM.qry_produtos.Insert;
        DM.qry_produtosidentificador.AsString := CSV.Cells[ColumnIndex, i];
        DM.qry_produtosstatus.AsString:='pending';
        DM.qry_produtos.Post;
      end;
    end;



    if campo = 'GTIN' then begin
      // primeiro verifica se esse identificador ja foi informado
      with DM.qry_produtos do
      begin
        Close;
        SQL.Clear;
        SQL.Add('SELECT * FROM produtos WHERE identificador = '+QuotedStr(CSV.Cells[ColumnIndexIND, i]));
        Open;
      end;
      if DM.qry_produtos.RecordCount > 0 then begin
        DM.qry_produtos.Edit;
        DM.qry_produtoscodigo_barras.AsString := CSV.Cells[ColumnIndex, i];
        DM.qry_produtos.Post;
      end else begin
        // cria log de não encontrar o produto na DB
      end;
    end;



    if campo = 'NOME' then begin
      // primeiro verifica se esse identificador ja foi informado
      with DM.qry_produtos do
      begin
        Close;
        SQL.Clear;
        SQL.Add('SELECT * FROM produtos WHERE identificador = '+QuotedStr(CSV.Cells[ColumnIndexIND, i]));
        Open;
      end;
      if DM.qry_produtos.RecordCount > 0 then begin
        DM.qry_produtos.Edit;
        DM.qry_produtosnome.AsString := CSV.Cells[ColumnIndex, i];
        DM.qry_produtos.Post;
      end else begin
        // cria log de não encontrar o produto na DB
      end;
    end;



    if campo = 'DESC' then begin
      // primeiro verifica se esse identificador ja foi informado
      with DM.qry_produtos do
      begin
        Close;
        SQL.Clear;
        SQL.Add('SELECT * FROM produtos WHERE identificador = '+QuotedStr(CSV.Cells[ColumnIndexIND, i]));
        Open;
      end;
      if DM.qry_produtos.RecordCount > 0 then begin
        DM.qry_produtos.Edit;
        DM.qry_produtosdescricao.AsString := CSV.Cells[ColumnIndex, i];
        DM.qry_produtos.Post;
      end else begin
        // cria log de não encontrar o produto na DB
      end;
    end;



    if campo = 'PRECO' then begin
      // verifica integridade do dado
      try

        // primeiro verifica se esse identificador ja foi informado
        with DM.qry_produtos do
        begin
          Close;
          SQL.Clear;
          SQL.Add('SELECT * FROM produtos WHERE identificador = '+QuotedStr(CSV.Cells[ColumnIndexIND, i]));
          Open;
        end;
        if DM.qry_produtos.RecordCount > 0 then begin
          DM.qry_produtos.Edit;
          DM.qry_produtospreco.AsFloat := StrToFloat(TrocaVirgPPto(CSV.Cells[ColumnIndex, i]));
          DM.qry_produtos.Post;
        end else begin
          // cria log de não encontrar o produto na DB
        end;

      except
        // grava log
        DM.qry_produtos.Edit;
        DM.qry_produtosstatus.AsString:='error';
        DM.qry_produtos.Post;
      end;
    end;



    if campo = 'ESTOQ' then begin
      // primeiro verifica se esse identificador ja foi informado
      try

        with DM.qry_produtos do
        begin
          Close;
          SQL.Clear;
          SQL.Add('SELECT * FROM produtos WHERE identificador = '+QuotedStr(CSV.Cells[ColumnIndexIND, i]));
          Open;
        end;
        if DM.qry_produtos.RecordCount > 0 then begin
          DM.qry_produtos.Edit;
          DM.qry_produtosquantidade.AsFloat := StrToFloat(TrocaVirgPPto(CSV.Cells[ColumnIndex, i]));
          DM.qry_produtos.Post;
        end else begin
          // cria log de não encontrar o produto na DB
        end;

      except
        // grava log
      end;
    end;



    if campo = 'IMG' then begin
      // primeiro verifica se esse identificador ja foi informado
      with DM.qry_produtos do
      begin
        Close;
        SQL.Clear;
        SQL.Add('SELECT * FROM produtos WHERE identificador = '+QuotedStr(CSV.Cells[ColumnIndexIND, i]));
        Open;
      end;
      if DM.qry_produtos.RecordCount > 0 then begin
        DM.qry_produtos.Edit;
        DM.qry_produtospath_img.AsString := CSV.Cells[ColumnIndex, i];
        DM.qry_produtos.Post;
      end else begin
        // cria log de não encontrar o produto na DB
      end;
    end;

    ProgressBar1.Position :=ProgressBar1.Position + 1;
    Application.ProcessMessages;
  end;
end;

end.

