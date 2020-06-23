import React from 'react';

import { 
    Container,
    Row,
    Col,
    Card,
    ButtonToolbar,
    Button,
    ButtonGroup,
    CardBody,
    CardFooter,
    Table,
    TabPane,
    Nav,
    NavItem,
    Pagination,
    PaginationLink,
    PaginationItem,
    UncontrolledTooltip,
    UncontrolledTabs
} from '../../components';

import { HeaderMain } from "../components/HeaderMain";
import { TrTableUsers } from "./components/TrTableUsers";

const Users = () => (
    <React.Fragment>
        <div>
            <HeaderMain 
                title="Utilisateurs"
                className="mb-5 mt-4"
            />
            { /* START Content */}
            <Row>
                <Col lg={ 12 }>
                    <Card className="mb-3">
                        <UncontrolledTabs initialActiveTabId="actives">
                            <CardBody>
                                <div className="d-flex">
                                    <Nav pills>
                                        <NavItem>
                                            <UncontrolledTabs.NavLink tabId="actives" onClick={ () => { alert('Yes') } }>
                                                Actifs
                                            </UncontrolledTabs.NavLink>
                                        </NavItem>
                                        <NavItem>
                                            <UncontrolledTabs.NavLink tabId="pending">
                                                En attente
                                            </UncontrolledTabs.NavLink>
                                        </NavItem>
                                        <NavItem>
                                            <UncontrolledTabs.NavLink tabId="suspended">
                                                Suspendus
                                            </UncontrolledTabs.NavLink>
                                        </NavItem>
                                    </Nav>
                                    <ButtonToolbar className="ml-auto">
                                        <ButtonGroup>
                                            <Button color="link" className="align-self-center mr-2 text-decoration-none" id="tooltipSettings">
                                                <i className="fa fa-fw fa-gear"></i>
                                            </Button>
                                        </ButtonGroup>
                                        <ButtonGroup>
                                            <Button color="primary" className="align-self-center" id="tooltipAddNew">
                                                <i className="fa fa-fw fa-plus"></i>
                                            </Button>
                                        </ButtonGroup>
                                    </ButtonToolbar>
                                    <UncontrolledTooltip placement="right" target="tooltipAddNew">
                                        Add New
                                    </UncontrolledTooltip>
                                    <UncontrolledTooltip placement="right" target="tooltipSettings">
                                        Settings
                                    </UncontrolledTooltip>
                                </div>
                            </CardBody>

                            <UncontrolledTabs.TabContent>
                                <TabPane tabId="actives">
                                    { /* START Table */}
                                    <Table className="mb-0" hover responsive>
                                        <thead>
                                            <tr>
                                                <th className="bt-0">Name</th>
                                                <th className="bt-0">Email</th>
                                                <th className="text-right bt-0">Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <TrTableUsers />
                                        </tbody>
                                    </Table>
                                    { /* END Table */}
                                </TabPane>
                               
                                <TabPane tabId="pending">
                                    { /* START Table */}
                                    <Table className="mb-0" hover responsive>
                                        <thead>
                                            <tr>
                                                <th className="bt-0">Name</th>
                                                <th className="bt-0">Email</th>
                                                <th className="text-right bt-0">Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <TrTableUsers />
                                        </tbody>
                                    </Table>
                                    { /* END Table */}
                                </TabPane>


                                <TabPane tabId="suspended">
                                    { /* START Table */}
                                    <Table className="mb-0" hover responsive>
                                        <thead>
                                            <tr>
                                                <th className="bt-0">Name</th>
                                                <th className="bt-0">Email</th>
                                                <th className="text-right bt-0">Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <TrTableUsers />
                                        </tbody>
                                    </Table>
                                    { /* END Table */}
                                </TabPane>

                            </UncontrolledTabs.TabContent>
                        </UncontrolledTabs>

                        <CardFooter className="d-flex">
                            <span className="align-self-center">
                                Showing 1 to 10 of 57 entries
                            </span>
                            <Pagination aria-label="Page navigation example" className="ml-auto">
                                <PaginationItem>
                                    <PaginationLink previous href="#">
                                        <i className="fa fa-fw fa-angle-left"></i>
                                    </PaginationLink>
                                </PaginationItem>
                                <PaginationItem active>
                                    <PaginationLink href="#">
                                        1
                                    </PaginationLink>
                                </PaginationItem>
                                <PaginationItem>
                                    <PaginationLink href="#">
                                        2
                                    </PaginationLink>
                                </PaginationItem>
                                <PaginationItem>
                                    <PaginationLink href="#">
                                        3
                                    </PaginationLink>
                                </PaginationItem>
                                <PaginationItem>
                                    <PaginationLink next href="#">
                                        <i className="fa fa-fw fa-angle-right"></i>
                                    </PaginationLink>
                                </PaginationItem>
                            </Pagination>
                        </CardFooter>
                    </Card>
                </Col>
            </Row>
            { /* END Content */}

        </div>
    </React.Fragment>
);

export default Users;