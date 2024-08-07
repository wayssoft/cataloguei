unit uPrincipal;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, ExtCtrls, ComCtrls,
  DBGrids, Buttons, StdCtrls, Grids, ActnList, ZConnection, ZDataset, LCLIntf,
  // units
  // Uses para ultilizar o Request4Pascal
  uRequest4pascal,
  fpjson,
  jsonparser,
  //Formularios
  uLogin, uConex, uFinish, uFiltro,
  uImportarCSV, DB;

type

  { TfrmPrincipal }

  TfrmPrincipal = class(TForm)
    act_enabled_bt: TAction;
    act_disbled_bt: TAction;
    act_start_timer: TAction;
    act_atualizar_lista: TAction;
    ActionList1: TActionList;
    ds_produtos: TDataSource;
    Image1: TImage;
    Image2: TImage;
    Label1: TLabel;
    Label2: TLabel;
    Label3: TLabel;
    lbLinkCatalago: TLabel;
    Label4: TLabel;
    mB64: TMemo;
    mLog: TMemo;
    PageControl1: TPageControl;
    Panel1: TPanel;
    Panel4: TPanel;
    ProgressBar1: TProgressBar;
    spBtImportarProdutos: TSpeedButton;
    spBtFiltro: TSpeedButton;
    spBtSync: TSpeedButton;
    SpeedButton1: TSpeedButton;
    StringGrid1: TStringGrid;
    TabSheet2: TTabSheet;
    TabSheet3: TTabSheet;
    Timer1: TTimer;
    procedure act_atualizar_listaExecute(Sender: TObject);
    procedure act_disbled_btExecute(Sender: TObject);
    procedure act_enabled_btExecute(Sender: TObject);
    procedure act_start_timerExecute(Sender: TObject);
    procedure FormCreate(Sender: TObject);
    procedure FormShow(Sender: TObject);
    procedure lbLinkCatalagoClick(Sender: TObject);
    procedure spBtImportarProdutosClick(Sender: TObject);
    procedure spBtFiltroClick(Sender: TObject);
    procedure spBtSyncClick(Sender: TObject);
    procedure SpeedButton1Click(Sender: TObject);
    procedure StringGrid1DblClick(Sender: TObject);
    procedure Timer1Timer(Sender: TObject);
  private
    var
    total_produtos,
    progress_produto:Integer;
    TotError,
    TotSync:Integer;
  public

  end;

var
  frmPrincipal: TfrmPrincipal;

implementation

{$R *.lfm}

{ TfrmPrincipal }

// Função para ajustar o formato da moeda
function AjustarValorMoeda(const Valor: string): string;
var
  Temp: string;
  i: Integer;
begin
  // Remove os pontos do valor
  Temp := '';
  for i := 1 to Length(Valor) do
    if Valor[i] <> '.' then
      Temp := Temp + Valor[i];

  // Substitui a vírgula por ponto
  Temp := StringReplace(Temp, ',', '.', [rfReplaceAll]);

  Result := Temp;
end;

procedure TfrmPrincipal.FormCreate(Sender: TObject);
begin
  // cria a conexão com banco de dados
  DM.ZConnection1.Database:=ExtractFilePath(ParamStr(0))+'data.db';
  DM.ZConnection1.Connected:=True;
end;

procedure TfrmPrincipal.act_atualizar_listaExecute(Sender: TObject);
var
  i, // linha
  j: // coluna
  Integer;
begin
  // atualiza a tabela
  with DM.qry_produtos do begin
     Refresh;
     Last;
     First;
  end;

  // Definindo o número de linhas e colunas (opcional se já estiver definido visualmente)
  StringGrid1.RowCount := DM.qry_produtos.RecordCount + 1; // número de linhas
  StringGrid1.ColCount := 7; // número de colunas

  // Ajusta o cabecalho
  i:=0;
  j:=0;
  StringGrid1.Cells[0, i] := 'identificador';
  StringGrid1.Cells[1, i] := 'Codigo barras';
  StringGrid1.Cells[2, i] := 'Nome';
  StringGrid1.Cells[3, i] := 'Descrição';
  StringGrid1.Cells[4, i] := 'Preço';
  StringGrid1.Cells[5, i] := 'Estoque';
  StringGrid1.Cells[6, i] := 'Status';

  // ajustar largura das colunas
  StringGrid1.ColWidths[0] := 110;
  StringGrid1.ColWidths[1] := 110;
  StringGrid1.ColWidths[2] := 210;
  StringGrid1.ColWidths[3] := 300;
  StringGrid1.ColWidths[4] := 60;
  StringGrid1.ColWidths[5] := 60;
  StringGrid1.ColWidths[6] := 80;

  while not DM.qry_produtos.EOF do begin
    i:=i+1;
    StringGrid1.Cells[0, i] := DM.qry_produtosidentificador.AsString;
    StringGrid1.Cells[1, i] := DM.qry_produtoscodigo_barras.AsString;
    StringGrid1.Cells[2, i] := DM.qry_produtosnome.AsString;
    StringGrid1.Cells[3, i] := DM.qry_produtosdescricao.AsString;
    StringGrid1.Cells[4, i] := 'R$ '+FormatFloat('0.00',DM.qry_produtospreco.AsFloat);
    StringGrid1.Cells[5, i] := FloatToStr(DM.qry_produtosquantidade.AsFloat);
    StringGrid1.Cells[6, i] := DM.qry_produtosstatus.AsString;
    DM.qry_produtos.Next;
  end;

end;

procedure TfrmPrincipal.act_disbled_btExecute(Sender: TObject);
begin
  spBtFiltro.Enabled:=False;
  spBtImportarProdutos.Enabled:=False;
  spBtSync.Enabled:=False;
end;

procedure TfrmPrincipal.act_enabled_btExecute(Sender: TObject);
begin
  spBtFiltro.Enabled:=True;
  spBtImportarProdutos.Enabled:=True;
  spBtSync.Enabled:=True;
end;

procedure TfrmPrincipal.act_start_timerExecute(Sender: TObject);
begin
  DM.qry_produtos.Refresh;
  DM.qry_produtos.Last;
  DM.qry_produtos.First;
  ProgressBar1.Max:=DM.qry_produtos.RecordCount;
  ProgressBar1.Position:=1;
  Timer1.Enabled:=True;
  total_produtos:=DM.qry_produtos.RecordCount;
  progress_produto:=1;
end;

procedure TfrmPrincipal.FormShow(Sender: TObject);
begin
  TabSheet2.Show;
  If (frmLogin = Nil) Then // Verifica se o formulário está vazio (Nil).
  frmLogin := TfrmLogin.Create(Application); // Cria o formulário.
  frmLogin.WindowState := wsNormal; // Se o usuario maximizou ou minizou o formulário, ele volta para o tamanho nornal.
  frmLogin.ShowModal; //ou Form1.ShowModal - Mostra o formulário na tela.
  with DM.qry_produtos do
  begin
     Close;
     SQL.Clear;
     SQL.Add('SELECT * FROM produtos');
     Open;
  end;
  act_atualizar_lista.Execute;
  lbLinkCatalago.Caption:='https://app.cataloguei.shop/d?loja='+DM.dominio;
end;

procedure TfrmPrincipal.lbLinkCatalagoClick(Sender: TObject);
begin
  OpenURL(lbLinkCatalago.Caption);
end;

procedure TfrmPrincipal.spBtImportarProdutosClick(Sender: TObject);
begin
  If (FrmImportarCsvProdutos = Nil) Then // Verifica se o formulário está vazio (Nil).
  FrmImportarCsvProdutos := TFrmImportarCsvProdutos.Create(Application); // Cria o formulário.
  FrmImportarCsvProdutos.WindowState := wsNormal; // Se o usuario maximizou ou minizou o formulário, ele volta para o tamanho nornal.
  FrmImportarCsvProdutos.ShowModal; //ou Form1.ShowModal - Mostra o formulário na tela.
  with DM.qry_produtos do begin
     Close;
     SQL.Clear;
     SQL.Add('SELECT * FROM produtos');
     Open;
  end;
  act_atualizar_lista.Execute;
end;

procedure TfrmPrincipal.spBtFiltroClick(Sender: TObject);
begin
  frmFiltro.showmodal;
  act_atualizar_lista.Execute;
end;

procedure TfrmPrincipal.spBtSyncClick(Sender: TObject);
begin
  // desfaz filtro
  ShowMessage('Atenção para a sincronização dos produtos o sistema desfará qualquer filtro aplicado');
  with DM.qry_produtos do begin
     Close;
     SQL.Clear;
     SQL.Add('SELECT * FROM produtos');
     Open;
  end;
  TotError:=0;
  TotSync:=0;
  act_atualizar_lista.Execute;
  act_disbled_bt.Execute;
  act_start_timer.Execute;
end;

procedure TfrmPrincipal.SpeedButton1Click(Sender: TObject);
begin
  OpenURL('https://wayssoft.tomticket.com/kb/cataloguei-desktop');
end;

procedure TfrmPrincipal.StringGrid1DblClick(Sender: TObject);
var
  valorPrimeiraColuna: string;
begin
  // Verifica se há alguma linha selecionada
  if StringGrid1.Row >= 0 then
  begin
    // Obtém o valor da primeira coluna da linha selecionada
    valorPrimeiraColuna := StringGrid1.Cells[0, StringGrid1.Row];
    // filtra na base de dados o registro
    with DM.qry_produtos_open_link do
    begin
      Close;
      SQL.Clear;
      SQL.Add('SELECT * FROM produtos WHERE identificador = '+QuotedStr(valorPrimeiraColuna));
      Open;
    end;
    if DM.qry_produtos_open_link.RecordCount =0 then begin

    end else begin
      if DM.qry_produtos_open_linkid_produto_cataloguei.AsInteger > 0 then begin
        OpenURL('https://app.cataloguei.shop/d.php?produto='+IntToStr(DM.qry_produtos_open_linkid_produto_cataloguei.AsInteger)+'&loja='+DM.dominio);
      end else begin
        ShowMessage('Produto ainda não foi sincronizado.');
      end;
    end;

    // Exemplo de uso do valor obtido
    //ShowMessage('Valor da primeira coluna na linha ' + IntToStr(StringGrid1.Row) + ': ' + valorPrimeiraColuna);
  end;
end;

procedure TfrmPrincipal.Timer1Timer(Sender: TObject);
var
  url: String;
  body: String;
  headers: TStrings;
  JSONResponse: TJSONObject;
  request: TRequest4pascal;
  error:Boolean;
  log:String;
begin
  Timer1.Enabled:=False;
  error:=False;

  // verifica se imagem existe
  if not FileExists(DM.qry_produtospath_img.AsString) then
  begin
      error:=True;
      mLog.Lines.Add('ERROR: Imagem produto '+DM.qry_produtosidentificador.AsString
                                      +' não existe caminho:'+DM.qry_produtospath_img.AsString);
  end else begin
      // verifica extesão da imagem
      if UpperCase(ExtractFileExt(DM.qry_produtospath_img.AsString)) = '.JPG' then begin;

        request := TRequest4pascal.Create;
        try
          mB64.Lines.Text:=request.ImageToBase64(DM.qry_produtospath_img.AsString);
        finally
          request.Free;
        end;

      end else begin
        error:=True;
        mLog.Lines.Add('ERROR: Imagem produto '+DM.qry_produtosidentificador.AsString
                                        +' extensão diferente de JPG:'+DM.qry_produtospath_img.AsString);
      end;

  end;

  // verifica se ja esta atualizado o registro
  if DM.qry_produtosstatus.AsString = 'atualizado' then begin
    error:=True;
    mLog.Lines.Add('ERROR: produto: '+DM.qry_produtosidentificador.AsString+' já esta atualizado');
  end;

  if DM.qry_produtosstatus.AsString = 'error' then begin
    error:=True;
    mLog.Lines.Add('ERROR: produto: '+DM.qry_produtosidentificador.AsString+' com erro');
  end;

  if error = False then begin;

      url := 'https://wayssoft.com.br/api/v1/cataloguei/send_produto.php?token='+DM.qry_configtoken.AsString;
      body := '{'
      +'"identificador":"'+DM.qry_produtosidentificador.AsString+'",'
      +'"codigo_barras":"'+DM.qry_produtoscodigo_barras.AsString+'",'
      +'"nome":"'+DM.qry_produtosnome.AsString+'",'
      +'"descricao":"'+DM.qry_produtosdescricao.AsString+'",'
      +'"preco":"'+AjustarValorMoeda(FormatFloat('0.00',DM.qry_produtospreco.AsFloat))+'",'
      +'"estoque":"'+AjustarValorMoeda(FloatToStr(DM.qry_produtosquantidade.AsFloat))+'",'
      +'"imgB64":"'+mB64.Lines.Text+'"'
      +'}';
      mLog.Lines.Text:=body;
      headers := TStringList.Create;
      headers.Add('Authorization: Bearer your_token_here');

      request := TRequest4pascal.Create;
      try
        JSONResponse := request._post(url, body, headers);
        // Processar a resposta JSON
        // mostra o resultado
        if JSONResponse.IndexOfName('status') <> -1 then
        begin
          if JSONResponse.Get('status') = 'success' then begin
            //atualizar status do produto
            DM.qry_produtos.Edit;
            DM.qry_produtosstatus.AsString:='atualizado';
            DM.qry_produtosid_produto_cataloguei.AsInteger:=StrToInt(JSONResponse.Get('id_produto'));
            DM.qry_produtos.Post;
            TotSync:=TotSync+1;
          end;
        end;
      finally
        headers.Free;
        request.Free;
      end;

  end else begin
     TotError:=TotError+1;
  end;

  Application.ProcessMessages;
  ProgressBar1.Position:= ProgressBar1.Position+1;
  DM.qry_produtos.Next;

  // verifica se foi atualizado todos os produtos
  if progress_produto >= total_produtos then begin
    // salva o log
    log:=ExtractFilePath(ParamStr(0))+'log\'+FormatDateTime('ddmmyyyyhhnnss', now)+'.txt';
    mLog.Lines.SaveToFile(log);
    frmFinish.log := log;
    frmFinish.TotError:=TotError;
    frmFinish.TotSync:=TotSync;
    frmFinish.showmodal;
    ProgressBar1.Position:=0;
    act_enabled_bt.Execute;
    act_atualizar_lista.Execute;
  end else begin
    progress_produto := progress_produto + 1;
    Timer1.Enabled:=True;
  end;
end;

end.

