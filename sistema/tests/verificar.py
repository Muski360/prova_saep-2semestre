"""Verificação local: servidor PHP em 127.0.0.1:8000 e banco já importado."""
from pathlib import Path
import http.cookiejar
import urllib.request
import urllib.parse
import re
import subprocess
import json
import uuid

ROOT = Path(__file__).resolve().parents[2]
BASE = 'http://127.0.0.1:8000'
cookies = http.cookiejar.CookieJar()
client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cookies))
marker = 'TesteSupVet_' + uuid.uuid4().hex[:10]

def request(path, data=None):
    body = urllib.parse.urlencode(data).encode() if data is not None else None
    with client.open(BASE + path, body) as res:
        html = res.read().decode('utf-8')
        assert res.status == 200
        assert not re.search(r'Fatal error|Warning:|Uncaught|Deprecated:', html), html
        return html, res.url

def token(html):
    return re.search(r'name="csrf_token" value="([^"]+)"', html)[1]

def php(code):
    prefix = "require 'sistema/vendor/autoload.php'; $pdo=App\\Core\\ConexaoBanco::obterConexao('sistema/config/database.ini'); "
    return subprocess.check_output(['php', '-r', prefix + code], cwd=ROOT, text=True, encoding='utf-8')

try:
    _, url = request('/tutores')
    assert url.endswith('/login'), url
    html, _ = request('/login')
    html, _ = request('/login', {'email': 'gestao@supvet.com', 'senha': 'errada', 'csrf_token': token(html)})
    assert 'incorretos' in html
    html, url = request('/login', {'email': 'gestao@supvet.com', 'senha': '123456', 'csrf_token': token(html)})
    assert url == BASE + '/' and 'Mariana Rocha' in html and 'SupVet' in html
    csrf = token(html)
    for path in ['/tutores', '/pets', '/agendamentos', '/tutores/novo', '/pets/novo', '/agendamentos/novo', '/agendamentos/editar-data?id=1']:
        request(path)
    html, _ = request('/tutores?q=Carlos')
    assert 'Carlos Eduardo Silva' in html and 'Beatriz Souza Martins' not in html
    html, _ = request('/tutores/salvar', {'csrf_token': csrf, 'nome': marker, 'cpf': '11111111111', 'telefone': '11999999999', 'email': marker+'@teste.com'})
    assert 'CPF válido' in html
    html, _ = request('/tutores/salvar', {'csrf_token': 'invalido', 'nome': marker})
    assert 'expirado' in html
    request('/tutores/salvar', {'csrf_token': csrf, 'nome': marker, 'cpf': '52998224725', 'telefone': '11999999999', 'email': marker+'@teste.com'})
    tutor = json.loads(php("echo json_encode($pdo->query(\"SELECT * FROM tutores WHERE nome = '"+marker+"'\")->fetch());"))
    assert tutor and tutor['cpf_criptografado'] != '52998224725'
    assert php("echo App\\Core\\Criptografia::decifrar('"+tutor['cpf_criptografado']+"');") == '52998224725'
    tid = tutor['id']
    request('/tutores/atualizar', {'csrf_token': csrf, 'id': tid, 'nome': marker, 'cpf': '11144477735', 'telefone': '11988888888', 'email': marker+'@teste.com'})
    html, _ = request('/tutores/editar?id='+str(tid))
    assert '11144477735' in html and '11988888888' in html
    # Tutor sem pets pode ser excluído.
    request('/tutores/excluir', {'csrf_token': csrf, 'id': tid})
    assert php("echo $pdo->query('SELECT COUNT(*) FROM tutores WHERE id = "+str(tid)+"')->fetchColumn();") == '0'
    request('/tutores/salvar', {'csrf_token': csrf, 'nome': marker, 'cpf': '52998224725', 'telefone': '11999999999', 'email': marker+'@teste.com'})
    tid = int(php("echo $pdo->query(\"SELECT id FROM tutores WHERE nome = '"+marker+"'\")->fetchColumn();"))
    pet = {'csrf_token': csrf, 'tutor_id': tid, 'nome': marker, 'especie': 'Canina', 'raca': 'SRD', 'data_nascimento': '2020-01-01'}
    html, _ = request('/pets/salvar', {**pet, 'tutor_id': 99999999})
    assert 'tutor existente' in html
    request('/pets/salvar', pet)
    pid = int(php("echo $pdo->query(\"SELECT id FROM pets WHERE nome = '"+marker+"'\")->fetchColumn();"))
    html, _ = request('/tutores/excluir', {'csrf_token': csrf, 'id': tid})
    assert 'pets vinculados' in html
    agenda = {'csrf_token': csrf, 'pet_id': pid, 'data_agendamento': '2027-01-20', 'hora_agendamento': '10:30', 'servico': marker}
    html, _ = request('/agendamentos/salvar', {**agenda, 'data_agendamento': '2027-02-30'})
    assert 'válidos' in html
    request('/agendamentos/salvar', agenda)
    aid = int(php("echo $pdo->query(\"SELECT id FROM agendamentos WHERE servico = '"+marker+"'\")->fetchColumn();"))
    request('/agendamentos/atualizar-data', {'csrf_token': csrf, 'id': aid, 'data_agendamento': '2027-01-21', 'hora_agendamento': '11:45'})
    html, _ = request('/agendamentos/editar-data?id='+str(aid))
    assert '2027-01-21' in html and '11:45' in html
    request('/agendamentos/atualizar-data', {'csrf_token': csrf, 'id': aid, 'data_agendamento': '2027-01-22'})
    html, _ = request('/agendamentos/editar-data?id='+str(aid))
    assert '2027-01-22' in html and '11:45' in html
    html, _ = request('/agendamentos')
    for value in [marker, '52998224725', 'SRD', 'Dados do tutor', 'Dados do pet']:
        assert value in html
    dates = json.loads(php("echo json_encode(array_column(App\\Models\\Agendamento::listarPorData(), 'data_agendamento'));"))
    assert dates == sorted(dates)
    # Testa a expiração sem aguardar 15 minutos, envelhecendo a sessão local.
    sid = next(c.value for c in cookies if c.name == 'supvet_session')
    php("session_name('supvet_session'); session_id('"+sid+"'); session_start(); $_SESSION['ultimo_acesso']=time()-901; session_write_close();")
    html, url = request('/pets')
    assert 'expirada=1' in url and 'expirou' in html
    html, _ = request('/login')
    html, _ = request('/login', {'email': 'gestao@supvet.com', 'senha': '123456', 'csrf_token': token(html)})
    request('/logout', {'csrf_token': token(html)})
    _, url = request('/agendamentos')
    assert url.endswith('/login')
    print('OK: login, rotas protegidas, busca, cadastro/edição/exclusão, CPF cifrado, pet vinculado, agendamento, alteração de data, ordenação, CSRF, expiração e logout.')
finally:
    php("$pdo->beginTransaction(); $pdo->exec(\"DELETE FROM agendamentos WHERE servico = '"+marker+"'\"); $pdo->exec(\"DELETE FROM pets WHERE nome = '"+marker+"'\"); $pdo->exec(\"DELETE FROM tutores WHERE nome = '"+marker+"'\"); $pdo->commit();")
