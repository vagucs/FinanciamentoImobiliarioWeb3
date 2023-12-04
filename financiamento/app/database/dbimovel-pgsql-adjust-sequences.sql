SELECT setval('dbcomprador_id_seq', coalesce(max(id),0) + 1, false) FROM dbcomprador;
SELECT setval('dbcontrato_id_seq', coalesce(max(id),0) + 1, false) FROM dbcontrato;
SELECT setval('dbfiador_id_seq', coalesce(max(id),0) + 1, false) FROM dbfiador;
SELECT setval('dbfinanciador_id_seq', coalesce(max(id),0) + 1, false) FROM dbfinanciador;
SELECT setval('dbfinanciamentos_id_seq', coalesce(max(id),0) + 1, false) FROM dbfinanciamentos;
SELECT setval('dbinteracoes_id_seq', coalesce(max(id),0) + 1, false) FROM dbinteracoes;