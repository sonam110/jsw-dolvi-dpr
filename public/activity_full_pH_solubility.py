import numpy as np
import json
#pH_1.2

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/Hydrochloric_acid/COSMO_TZVPD/Hydrochloric_acid_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_chloride/COSMO_TZVPD/Sodium_chloride_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.4, 0.1, 0.5])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.4, 0.1, 0.5])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.4, 0.1, 0.5])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.4, 0.1, 0.5])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])


raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'pH_1.2', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)


import numpy as np
import json
#pH_4.5

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/aceticacid/COSMO_TZVPD/aceticacid_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.6, 0.4])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.6, 0.4])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.6, 0.4])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.6, 0.4])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])

raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'pH_4.5', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)


import numpy as np
import json
#pH_6.8

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/NaH2PO4/COSMO_TZVPD/NaH2PO4_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Na2HPO4/COSMO_TZVPD/Na2HPO4_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Hydrochloric_acid/COSMO_TZVPD/Hydrochloric_acid_c000.orcacosmo'])
x = np.array([0.0, 0.01, 0.01, 0.53, 0.45])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.01, 0.01, 0.53, 0.45])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.01, 0.01, 0.53, 0.45])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.01, 0.01, 0.53, 0.45])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])

raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'pH_6.8', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)


import numpy as np
import json
#pH_9

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/Ammonia/COSMO_TZVPD/Ammonia_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.25, 0.75])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.25, 0.75])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.25, 0.75])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.25, 0.75])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])


raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'pH_9', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)


import numpy as np
import json
#SGF_pH_1.6

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/Hydrochloric_acid/COSMO_TZVPD/Hydrochloric_acid_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_chloride/COSMO_TZVPD/Sodium_chloride_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Pepsin/COSMO_TZVPD/Pepsin_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.37, 0.008, 0.0032, 0.6188])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.37, 0.008, 0.0032, 0.6188])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.37, 0.008, 0.0032, 0.6188])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.37, 0.008, 0.0032, 0.6188])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])

raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'SGF_pH_1.6', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)


import numpy as np
import json
#SIF_pH_6.8

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/KH2PO4/COSMO_TZVPD/KH2PO4_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_chloride/COSMO_TZVPD/Sodium_chloride_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/NaOH/COSMO_TZVPD/NaOH_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.0068, 0.01, 0.0004, 0.9828])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.0068, 0.01, 0.0004, 0.9828])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.0068, 0.01, 0.0004, 0.9828])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.0068, 0.01, 0.0004, 0.9828])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])


raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'SIF_pH_6.8', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)


import numpy as np
import json
#FaSSGF_pH_1.6

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_taurocholate/COSMO_TZVPD/Sodium_taurocholate_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Lecithin/COSMO_TZVPD/Lecithin_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Pepsin/COSMO_TZVPD/Pepsin_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_chloride/COSMO_TZVPD/Sodium_chloride_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Hydrochloric_acid/COSMO_TZVPD/Hydrochloric_acid_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.000044, 0.000015, 0.0001, 0.001993, 0.000912, 0.996936])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.000044, 0.000015, 0.0001, 0.001993, 0.000912, 0.996936])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.000044, 0.000015, 0.0001, 0.001993, 0.000912, 0.996936])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.000044, 0.000015, 0.0001, 0.001993, 0.000912, 0.996936])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])

raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'FaSSGF_pH_1.6', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)


import numpy as np
import json
#FaSSIF_pH_6.5

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_taurocholate/COSMO_TZVPD/Sodium_taurocholate_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Lecithin/COSMO_TZVPD/Lecithin_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Maleicacid/COSMO_TZVPD/Maleicacid_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_chloride/COSMO_TZVPD/Sodium_chloride_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/NaOH/COSMO_TZVPD/NaOH_c000.orcacosmo'])
x = np.array([0.0, 0.171944, 0.016152, 0.236353, 0.427184, 0.148367])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.171944, 0.016152, 0.236353, 0.427184, 0.148367])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.171944, 0.016152, 0.236353, 0.427184, 0.148367])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.171944, 0.016152, 0.236353, 0.427184, 0.148367])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])


raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'FaSSIF_pH_6.5', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)


import numpy as np
import json
#FeSSIF_pH_5

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_taurocholate/COSMO_TZVPD/Sodium_taurocholate_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Lecithin/COSMO_TZVPD/Lecithin_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/aceticacid/COSMO_TZVPD/aceticacid_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_chloride/COSMO_TZVPD/Sodium_chloride_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/NaOH/COSMO_TZVPD/NaOH_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.0081, 0.0046, 0.0087, 0.0119, 0.004, 0.9627])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.0081, 0.0046, 0.0087, 0.0119, 0.004, 0.9627])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.0081, 0.0046, 0.0087, 0.0119, 0.004, 0.9627])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.0081, 0.0046, 0.0087, 0.0119, 0.004, 0.9627])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])


raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'FeSSIF_pH_5', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)


import numpy as np
import json
#acetate_buffer_pH_5

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_acetate/COSMO_TZVPD/Sodium_acetate_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/aceticacid/COSMO_TZVPD/aceticacid_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.00573, 0.00177, 0.9925])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.00573, 0.00177, 0.9925])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.00573, 0.00177, 0.9925])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.00573, 0.00177, 0.9925])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])

raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'acetate_buffer_pH_5', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)



import numpy as np
import json
#h2o

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 1.0])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 1.0])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 1.0])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 1.0])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])


raw_data = results['tot']['lng']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'h2o', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)

import numpy as np
import json

#ethanol

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Metformin/COSMO_TZVPD/Metformin_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/ethanol/COSMO_TZVPD/ethanol_c000.orcacosmo'])
x = np.array([0.0, 1.0])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 1.0])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 1.0])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 1.0])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])

raw_data = results['tot']['lng']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
data_dict = {'name': 'ethanol', 'data': dict(zip(keys, array_data.tolist()))}

# Convert to JSON
json_data = json.dumps(data_dict)

print(json_data)




