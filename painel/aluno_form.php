<?php
require_once __DIR__ . '/funcoes.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$ensinos = ['Fundamental I', 'Fundamental II', 'Ensino Médio', 'Ensino Médio Integral', 'EJA'];
$a = ['nome' => '', 'turma' => '', 'ensino' => $ensinos[0], 'telefone' => '', 'condicao' => 'Ativo'];

if ($id) {
    $l = consultar('SELECT nome, turma, ensino, telefone, condicao FROM estudante WHERE idestudante = ?', 'i', [$id]);
    if (!$l) {
        flash('danger', 'Aluno não encontrado.');
        redirecionar('alunos.php');
    }
    $a = $l[0];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!$id && preg_match('/^existente:(\d+)$/', $_POST['nome'] ?? '', $mm)) {
        flash('warning', 'Aluno já cadastrado: edite os dados abaixo em vez de duplicar.');
        redirecionar('aluno_form.php?id=' . (int) $mm[1]);
    }
    $a = [
        'nome' => trim(preg_replace('/\s+/u', ' ', $_POST['nome'] ?? '')),
        'turma' => trim($_POST['turma'] ?? ''),
        'ensino' => $_POST['ensino'] ?? '',
        'telefone' => telefone_fmt($_POST['telefone'] ?? ''),
        'condicao' => ($_POST['condicao'] ?? '') === 'Inativo' ? 'Inativo' : 'Ativo',
    ];
    foreach (['turma', 'ensino'] as $c) {
        $a[$c] = trim(preg_replace('/\s+/u', ' ', $a[$c]));
        if ($a[$c] !== '') {
            // Reaproveita a grafia já cadastrada (sem diferenciar maiúsculas).
            $ex = consultar("SELECT $c AS v FROM estudante WHERE $c = ? ORDER BY idestudante LIMIT 1", 's', [$a[$c]]);
            if ($c === 'ensino' && !$ex) {
                foreach ($ensinos as $padrao) {
                    if (mb_strtolower($padrao) === mb_strtolower($a[$c])) {
                        $ex = [['v' => $padrao]];
                    }
                }
            }
            if ($ex) {
                $a[$c] = $ex[0]['v'];
            }
        }
    }
    if ($a['nome'] === '') {
        flash('danger', 'Informe o nome do aluno.');
    } else {
        if ($id) {
            executar('UPDATE estudante SET nome=?, turma=?, ensino=?, telefone=?, condicao=? WHERE idestudante=?',
                'sssssi', [$a['nome'], $a['turma'], $a['ensino'], $a['telefone'], $a['condicao'], $id]);
        } else {
            executar('INSERT INTO estudante (nome, turma, ensino, telefone, condicao) VALUES (?,?,?,?,?)',
                'sssss', [$a['nome'], $a['turma'], $a['ensino'], $a['telefone'], $a['condicao']]);
        }
        flash('success', 'Aluno salvo com sucesso!');
        redirecionar('alunos.php');
    }
}

page_start($id ? 'Editar aluno' : 'Novo aluno');
$existentes = $id ? [] : consultar('SELECT idestudante, nome, turma FROM estudante ORDER BY nome, turma, idestudante');
$opcoes = ['turma' => [], 'ensino' => $ensinos];
foreach (['turma', 'ensino'] as $c) {
    foreach (array_column(consultar("SELECT DISTINCT $c AS v FROM estudante WHERE $c <> '' ORDER BY $c"), 'v') as $v) {
        if (!in_array(mb_strtolower($v), array_map('mb_strtolower', $opcoes[$c]), true)) {
            $opcoes[$c][] = $v;
        }
    }
    if ($a[$c] !== '' && !in_array($a[$c], $opcoes[$c], true)) {
        $opcoes[$c][] = $a[$c];
    }
}
?>
<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
<div class="card mb-3">
    <div class="card-body">
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="row">
                <div class="form-group col-md-6"><label>Nome<?php echo $id ? '' : ' <small class="text-muted">(selecione um aluno já cadastrado ou digite um novo)</small>'; ?></label>
                    <?php if ($id) { ?>
                        <input class="form-control" name="nome" required value="<?php echo e($a['nome']); ?>">
                    <?php } else { ?>
                        <select class="form-control" id="aluno-nome" name="nome" required style="width:100%">
                            <option value=""></option>
                            <?php foreach ($existentes as $x) { ?>
                                <option value="existente:<?php echo (int) $x['idestudante']; ?>"><?php echo e($x['nome'] . ' — ' . ($x['turma'] !== '' ? $x['turma'] : 'sem turma')); ?></option>
                            <?php } ?>
                            <?php if ($a['nome'] !== '') { ?><option selected><?php echo e($a['nome']); ?></option><?php } ?>
                        </select>
                    <?php } ?></div>
                <?php foreach (['turma' => 'Turma', 'ensino' => 'Tipo de ensino'] as $c => $rot) { ?>
                    <div class="form-group col-md-6"><label><?php echo $rot; ?> <small class="text-muted">(selecione ou digite um novo)</small></label>
                        <select class="form-control select-tags" name="<?php echo $c; ?>" style="width:100%">
                            <option value=""></option>
                            <?php foreach ($opcoes[$c] as $o) { ?>
                                <option<?php echo $o === $a[$c] ? ' selected' : ''; ?>><?php echo e($o); ?></option>
                            <?php } ?>
                        </select></div>
                <?php } ?>
                <div class="form-group col-md-6"><label>Telefone</label>
                    <input class="form-control" name="telefone" data-mask="tel" maxlength="14" placeholder="(xx)xxxxx-xxxx" value="<?php echo e(telefone_fmt($a['telefone'])); ?>"></div>
                <div class="form-group col-md-6"><label>Condição</label>
                    <select class="form-control" name="condicao">
                        <?php foreach (['Ativo', 'Inativo'] as $c) { ?>
                            <option<?php echo $c === $a['condicao'] ? ' selected' : ''; ?>><?php echo $c; ?></option>
                        <?php } ?>
                    </select></div>
            </div>
            <button class="btn btn-success">Salvar</button>
            <a href="alunos.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php page_end('<script src="assets/plugins/select2/js/select2.min.js"></script><script>$(".select-tags").select2({tags:true,allowClear:true,placeholder:"Selecione ou digite para cadastrar novo",createTag:function(p){var t=$.trim(p.term);if(t===""){return null;}var low=t.toLowerCase(),ex=null;$(this.$element).find("option").each(function(){if($(this).text().toLowerCase()===low){ex=true;}});return ex?null:{id:t,text:t};}});$("#aluno-nome").select2({tags:true,allowClear:true,placeholder:"Digite o nome ou selecione um aluno já cadastrado",createTag:function(p){var t=$.trim(p.term);return t===""?null:{id:t,text:t};}}).on("select2:select",function(e){var v=e.params.data.id;if(String(v).indexOf("existente:")===0&&confirm("Este aluno já está cadastrado ("+e.params.data.text+"). Abrir o cadastro dele para editar?")){location.href="aluno_form.php?id="+String(v).split(":")[1];}else if(String(v).indexOf("existente:")===0){$(this).val(null).trigger("change");}});</script>'); ?>
