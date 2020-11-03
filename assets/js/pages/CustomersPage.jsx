import React, {useEffect, useState} from "react";
import Pagination from "../components/Pagination";
import CustomersAPI from "../services/CustomersAPI";
import {Link} from "react-router-dom";
import {toast} from "react-toastify";
import TableLoader from "../components/loaders/TableLoader";
import SearchBar from "../components/forms/SearchBar";
import authAPI from "../services/authAPI";
import AlertWarningDelete from "../components/Alerts/AlertWarningDelete";
import AlertSuccess from "../components/Alerts/AlertSuccess";

const CustomersPage = (props) => {
    const [customers, setCustomers] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [search, setSearch] = useState("");
    const [loading, setLoading] = useState(true);

    //Permet d'aller recup les customers
    const fetchCustomers = async () => {
        try {
            const data = await CustomersAPI.findAll();
            setCustomers(data);
            setLoading(false);
        } catch (error) {
            toast.error("Impossible de charger les clients 😟");
        }
    };

    //Au chargement du composant, on va chercher les customers
    useEffect(() => {
        fetchCustomers();
    }, []);

    //Gestion Suppression d'un customer
    const handleDelete = async (id) => {
        const originalCustomers = [...customers];
        setCustomers(customers.filter((customer) => customer.id !== id));
        try {
            await CustomersAPI.delete(id);
            AlertSuccess({text:"Client Supprimé avec succès !"})
            toast.success("Le client a bien été supprimé 😀");
        } catch (error) {
            setCustomers(originalCustomers);
            toast.error(
                "Une erreur est survenue lors de la suppression d'un client 😟"
            );
        }
    };

    //Gestion du changement de page
    const handlePageChange = (page) => setCurrentPage(page);

    //Gestion de la recherche
    const handleSearch = (event) => {
        setSearch(event.currentTarget.value);
        setCurrentPage(1);
    };

    const itemsPerPage = 10;

    //Filtrage des customers en fonction de la recherche
    const filteredCustomers = customers.filter(
        (c) =>
            c.firstName.toLowerCase().includes(search.toLowerCase()) ||
            c.lastName.toLowerCase().includes(search.toLowerCase()) ||
            c.email.toLowerCase().includes(search.toLowerCase()) ||
            (c.company && c.company.toLowerCase().includes(search.toLowerCase()))
    );

    //Pagination des données
    const paginatedCustomers = Pagination.getData(
        filteredCustomers,
        currentPage,
        itemsPerPage
    );

    return (
        <>
            <div className="mb-3 d-flex justify-content-between align-items-center">
                <h1 className="fadeInLeftBig animated text-3xl">Liste des clients</h1>
                <Link
                    to="/customers/new"
                    className=" fadeInRightBig animated btn btn-label btn-primary blueBackGround rounded-pill shadow-material-2"
                >
                    <label htmlFor="">
                        <i className="ti-plus"></i>
                    </label>
                    Créer un client
                </Link>
            </div>
            <div className="form-inline">
                <SearchBar value={search} onChange={handleSearch}/>
            </div>
            {!loading && (
                <table className="table table-hover table-separated">
                    <thead>
                    <tr>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Entreprise</th>
                        <th className="text-center">Factures</th>
                        <th className="text-center">Montant total</th>
                        <th/>
                    </tr>
                    </thead>

                    <tbody>
                    {paginatedCustomers.map((customer) => (
                        <tr className="shadow-material-1 hover-shadow-material-2 table-perso hello" key={customer.id}>
                            <td>
                                <Link className="blueColor " to={"/customers/" + customer.id}>
                                    {customer.firstName} {customer.lastName}
                                </Link>
                            </td>
                            <td>{customer.email}</td>
                            <td>{customer.company}</td>
                            <td className="text-center">
                  <span className=" px-3 py-2 badge badge-secondary">
                    {customer.invoices.length}
                  </span>
                            </td>
                            <td className="text-center">
                                {customer.totalAmount.toLocaleString()} €
                            </td>
                            <td>
                                <button
                                    onClick={() => AlertWarningDelete({text: "Une fois supprimé, vous ne pourrez plus récupérer ce client !"}).then((willDelete) => {
                                        if (willDelete) {
                                            handleDelete(customer.id)
                                        }
                                    })}
                                    disabled={customer.invoices.length > 0}
                                    className="btn btn-sm btn-danger"
                                >
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            )}
            {loading && <TableLoader/>}
            {itemsPerPage < filteredCustomers.length && (
                <Pagination
                    currentPage={currentPage}
                    itemsPerPage={itemsPerPage}
                    length={filteredCustomers.length}
                    onPageChanged={handlePageChange}
                />
            )}
        </>
    );
};

export default CustomersPage;
